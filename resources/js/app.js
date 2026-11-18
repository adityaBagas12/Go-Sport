document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-dropdown-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const target = document.getElementById(toggle.dataset.dropdownToggle);
            if (target) {
                target.classList.toggle('hidden');
            }
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => {
            menu.classList.add('hidden');
        });
    });

    const mobileMenu = document.getElementById('mobile-menu');
    document.querySelectorAll('[data-mobile-menu-toggle]').forEach((button) => {
        button.addEventListener('click', () => mobileMenu?.classList.toggle('hidden'));
    });

    document.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => field.form?.submit());
    });

    document.querySelectorAll('[data-flash]').forEach((flash) => {
        setTimeout(() => {
            flash.style.transition = 'opacity .4s ease, transform .4s ease';
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-8px)';
            setTimeout(() => flash.remove(), 400);
        }, 4000);
    });

    document.querySelectorAll('[data-qty]').forEach((wrapper) => {
        const input = wrapper.querySelector('input[type="number"]');
        if (!input) return;

        wrapper.querySelector('[data-qty-decrease]')?.addEventListener('click', () => {
            input.value = Math.max(parseInt(input.min || '1', 10), (parseInt(input.value, 10) || 1) - 1);
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });

        wrapper.querySelector('[data-qty-increase]')?.addEventListener('click', () => {
            const max = parseInt(input.max, 10) || Infinity;
            const next = Math.min((parseInt(input.value, 10) || 1) + 1, max);
            if (next === parseInt(input.value, 10)) return;
            input.value = next;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    const cartForm = document.querySelector('[data-checkout-form]');

    if (cartForm) {
        const selectAll = document.querySelector('[data-cart-select-all]');
        const itemBoxes = Array.from(document.querySelectorAll('[data-cart-item]'));
        const summary = document.querySelector('[data-cart-summary]');
        const error = document.querySelector('[data-cart-error]');
        const checkoutLabel = document.querySelector('[data-cart-checkout-label]');

        const rupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

        const selected = () => itemBoxes.filter((box) => box.checked);

        const discountFor = (subtotal) => {
            if (!summary || summary.dataset.couponActive !== '1') return 0;
            if (subtotal < parseInt(summary.dataset.couponMin, 10)) return 0;
            if (summary.dataset.couponType === 'fixed') {
                return Math.min(parseInt(summary.dataset.couponValue, 10), subtotal);
            }
            return Math.floor((subtotal * parseInt(summary.dataset.couponValue, 10)) / 100);
        };

        const render = () => {
            const boxes = selected();
            const subtotal = boxes.reduce((sum, box) => sum + parseInt(box.dataset.subtotal, 10), 0);
            const quantity = boxes.reduce((sum, box) => sum + parseInt(box.dataset.quantity, 10), 0);
            const discount = discountFor(subtotal);

            if (summary) {
                summary.querySelector('[data-cart-count]').textContent = quantity;
                summary.querySelector('[data-cart-subtotal]').textContent = rupiah(subtotal);
                summary.querySelector('[data-cart-total]').textContent = rupiah(Math.max(subtotal - discount, 0));

                const row = summary.querySelector('[data-cart-discount-row]');
                row.classList.toggle('hidden', discount <= 0);
                row.querySelector('[data-cart-discount-code]').textContent = summary.dataset.couponCode;
                row.querySelector('[data-cart-discount]').textContent = '- ' + rupiah(discount);
            }

            if (checkoutLabel) {
                checkoutLabel.textContent = boxes.length ? `Checkout (${boxes.length})` : 'Checkout';
            }

            if (selectAll) {
                selectAll.checked = itemBoxes.length > 0 && boxes.length === itemBoxes.length;
                selectAll.indeterminate = boxes.length > 0 && boxes.length < itemBoxes.length;
            }
        };

        selectAll?.addEventListener('change', () => {
            itemBoxes.forEach((box) => {
                box.checked = selectAll.checked;
            });
            render();
        });

        itemBoxes.forEach((box) => box.addEventListener('change', render));

        cartForm.addEventListener('submit', (event) => {
            if (selected().length > 0) return;

            event.preventDefault();
            error?.classList.remove('hidden');
        });

        render();
    }

    document.querySelectorAll('[data-gallery]').forEach((gallery) => {
        const main = gallery.querySelector('[data-gallery-main]');
        gallery.querySelectorAll('[data-gallery-thumb]').forEach((thumb) => {
            thumb.addEventListener('click', () => {
                if (main) main.src = thumb.dataset.galleryThumb;
                gallery.querySelectorAll('[data-gallery-thumb]').forEach((t) => t.classList.remove('ring-brand-500'));
                thumb.classList.add('ring-brand-500');
            });
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const input = document.getElementById(toggle.dataset.passwordToggle);
            if (input) input.type = input.type === 'password' ? 'text' : 'password';
        });
    });

    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                const original = button.textContent;
                button.textContent = 'Tersalin!';
                setTimeout(() => (button.textContent = original), 1500);
            } catch (error) {
                // Clipboard not available.
            }
        });
    });

    // Quick Add Variant Modal
    const quickModal = document.getElementById('quick-variant-modal');
    if (quickModal) {
        const backdrop = document.getElementById('quick-variant-backdrop');
        const closeBtn = document.getElementById('quick-variant-close');
        const productIdInput = document.getElementById('quick-modal-product-id');
        const imageEl = document.getElementById('quick-modal-image');
        const nameEl = document.getElementById('quick-modal-name');
        const priceEl = document.getElementById('quick-modal-price');
        const stockEl = document.getElementById('quick-modal-stock');
        const sizesGroup = document.getElementById('quick-modal-sizes-group');
        const sizesContainer = document.getElementById('quick-modal-sizes-container');
        const colorsGroup = document.getElementById('quick-modal-colors-group');
        const colorsContainer = document.getElementById('quick-modal-colors-container');
        const qtyInput = document.getElementById('quick-modal-quantity');
        const submitBtn = document.getElementById('quick-modal-submit');

        let currentVariants = [];
        let basePriceFormatted = '';

        const closeQuickModal = () => {
            quickModal.classList.add('hidden');
            document.body.style.overflow = '';
        };

        const updateQuickModalState = () => {
            const checkedSize = quickModal.querySelector('input[name="size"]:checked')?.value || null;
            const checkedColor = quickModal.querySelector('input[name="color"]:checked')?.value || null;
            const hasSizes = !sizesGroup.classList.contains('hidden');
            const hasColors = !colorsGroup.classList.contains('hidden');

            if ((hasSizes && !checkedSize) || (hasColors && !checkedColor)) {
                stockEl.innerHTML = '<span class="text-[#5A6B61]">Silakan pilih ukuran & warna</span>';
                submitBtn.disabled = true;
                return;
            }

            const match = currentVariants.find((v) => {
                const sMatch = !hasSizes || (v.size && v.size.toLowerCase() === checkedSize?.toLowerCase());
                const cMatch = !hasColors || (v.color && v.color.toLowerCase() === checkedColor?.toLowerCase());
                return sMatch && cMatch;
            });

            if (!match) {
                stockEl.innerHTML = '<span class="text-[#E53935] font-bold">Kombinasi varian ini tidak tersedia</span>';
                submitBtn.disabled = true;
                qtyInput.max = 0;
                qtyInput.value = 0;
                return;
            }

            const stock = parseInt(match.stock, 10);
            if (stock > 0) {
                stockEl.innerHTML = `<span class="text-emerald-700 font-bold">Tersisa ${stock} unit</span>`;
                submitBtn.disabled = false;
                qtyInput.max = stock;
                if (parseInt(qtyInput.value, 10) > stock) qtyInput.value = stock;
                if (parseInt(qtyInput.value, 10) < 1) qtyInput.value = 1;
            } else {
                stockEl.innerHTML = '<span class="text-[#E53935] font-bold">Stok varian ini habis</span>';
                submitBtn.disabled = true;
                qtyInput.max = 0;
                qtyInput.value = 0;
            }

            if (match.price) {
                priceEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(match.price);
            } else {
                priceEl.textContent = basePriceFormatted;
            }
        };

        const openQuickModal = (data) => {
            productIdInput.value = data.productId;
            imageEl.src = data.productImage;
            imageEl.alt = data.productName;
            nameEl.textContent = data.productName;
            basePriceFormatted = data.productPrice;
            priceEl.textContent = basePriceFormatted;
            currentVariants = data.variants || [];
            qtyInput.value = 1;

            // Render Sizes
            sizesContainer.innerHTML = '';
            if (data.sizes && data.sizes.length > 0) {
                sizesGroup.classList.remove('hidden');
                data.sizes.forEach((size, idx) => {
                    const label = document.createElement('label');
                    label.className = 'cursor-pointer';
                    label.innerHTML = `
                        <input type="radio" name="size" value="${size}" class="peer sr-only" ${idx === 0 ? 'checked' : ''} required>
                        <span class="grid h-10 min-w-10 place-items-center rounded-xl border border-[#D5E0D8] bg-white px-3 text-xs font-bold text-[#1E2B24] transition peer-checked:border-[#4F7A63] peer-checked:bg-[#4F7A63]/10 peer-checked:text-[#4F7A63] hover:border-[#1E2B24]">
                            ${size}
                        </span>
                    `;
                    sizesContainer.appendChild(label);
                });
            } else {
                sizesGroup.classList.add('hidden');
            }

            // Render Colors
            colorsContainer.innerHTML = '';
            if (data.colors && data.colors.length > 0) {
                colorsGroup.classList.remove('hidden');
                data.colors.forEach((color, idx) => {
                    const label = document.createElement('label');
                    label.className = 'cursor-pointer';
                    label.innerHTML = `
                        <input type="radio" name="color" value="${color}" class="peer sr-only" ${idx === 0 ? 'checked' : ''} required>
                        <span class="inline-flex items-center rounded-xl border border-[#D5E0D8] bg-white px-3.5 py-2 text-xs font-bold text-[#1E2B24] transition peer-checked:border-[#4F7A63] peer-checked:bg-[#4F7A63]/10 peer-checked:text-[#4F7A63] hover:border-[#1E2B24]">
                            ${color}
                        </span>
                    `;
                    colorsContainer.appendChild(label);
                });
            } else {
                colorsGroup.classList.add('hidden');
            }

            quickModal.querySelectorAll('input[name="size"], input[name="color"]').forEach((input) => {
                input.addEventListener('change', updateQuickModalState);
            });

            updateQuickModalState();
            quickModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };

        document.querySelectorAll('[data-quick-variant]').forEach((btn) => {
            btn.addEventListener('click', () => {
                openQuickModal({
                    productId: btn.dataset.productId,
                    productName: btn.dataset.productName,
                    productImage: btn.dataset.productImage,
                    productPrice: btn.dataset.productPrice,
                    productRawPrice: btn.dataset.productRawPrice,
                    sizes: JSON.parse(btn.dataset.productSizes || '[]'),
                    colors: JSON.parse(btn.dataset.productColors || '[]'),
                    variants: JSON.parse(btn.dataset.productVariants || '[]'),
                });
            });
        });

        closeBtn?.addEventListener('click', closeQuickModal);
        backdrop?.addEventListener('click', closeQuickModal);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !quickModal.classList.contains('hidden')) {
                closeQuickModal();
            }
        });
    }
});
