document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('burger');
    const nav = document.getElementById('site-nav');

    if (burger && nav) {
        burger.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('open');
            burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('open');
                burger.setAttribute('aria-expanded', 'false');
            });
        });
    }

    function setupNewsletterForm(formId, inputId, messageId) {
        const form = document.getElementById(formId);
        const input = document.getElementById(inputId);
        const message = document.getElementById(messageId);

        if (!form || !input || !message) {
            return;
        }

        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const email = input.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email) {
                message.textContent = "Veuillez saisir une adresse email.";
                message.style.color = "#c53b16";
                return;
            }

            if (!emailRegex.test(email)) {
                message.textContent = "Veuillez entrer une adresse email valide.";
                message.style.color = "#c53b16";
                return;
            }

            localStorage.setItem('alma_newsletter_preview', email);
            message.textContent = "Votre inscription a bien été prise en compte pour l’aperçu local.";
            message.style.color = "#2f7a39";
            form.reset();
        });
    }

    setupNewsletterForm('newsletter-form-top', 'email-top', 'form-message-top');
    setupNewsletterForm('newsletter-form-bottom', 'email-bottom', 'form-message-bottom');


const scrollTopBtn = document.querySelector('.scroll-top');

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        scrollTopBtn.classList.add('show');
    } else {
        scrollTopBtn.classList.remove('show');
    }
});
});
