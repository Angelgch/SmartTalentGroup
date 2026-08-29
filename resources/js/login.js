document.addEventListener('DOMContentLoaded', () => {
    const tabLogin = document.getElementById('tabLogin');
    const tabCompany = document.getElementById('tabCompany');

    const loginCard = document.getElementById('loginCard');
    const companyCard = document.getElementById('companyCard');

    function switchTab(selectedTarget) {
        if (selectedTarget === loginCard) {
            tabLogin.classList.add('active');
            tabCompany.classList.remove('active');

            companyCard.classList.remove('show');
            companyCard.classList.add('d-none');

            loginCard.classList.remove('d-none');
            setTimeout(() => loginCard.classList.add('show'), 20);
        } else {
            tabCompany.classList.add('active');
            tabLogin.classList.remove('active');

            loginCard.classList.remove('show');
            loginCard.classList.add('d-none');

            companyCard.classList.remove('d-none');
            setTimeout(() => companyCard.classList.add('show'), 20);
        }
    }

    tabLogin.addEventListener('click', () => switchTab(loginCard));
    tabCompany.addEventListener('click', () => switchTab(companyCard));

    // Submit simulado del login
    document.getElementById('loginForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('loginEmail').value;
        if (email.includes('admin')) {
            window.location.href = '/admin/dashboard';
        } else {
            window.location.href = '/company/dashboard';
        }
    });
}); 