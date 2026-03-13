// Auth/Login JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.user-type-tab');
    const userTypeInput = document.getElementById('userType');
    
    if (tabs.length > 0 && userTypeInput) {
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                userTypeInput.value = this.dataset.type;
            });
        });
    }
});
