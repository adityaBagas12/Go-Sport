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
});
