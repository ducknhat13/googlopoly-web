import './bootstrap';

// Add custom scripts
document.addEventListener('DOMContentLoaded', function() {
    // Menu active state
    document.querySelectorAll('.left-side ul li a').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.left-side ul li a').forEach(link => 
                link.classList.remove('active')
            );
            this.classList.add('active');
        });
    });
});
