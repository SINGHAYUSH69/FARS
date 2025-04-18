
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');

    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('open');
}


function toggleDropdown() {
    const dropdown = document.getElementById('dropdown-content');
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('dropdown-content');
    const dropdownButton = document.querySelector('[onclick="toggleDropdown()"]');

    if (dropdownButton && !dropdownButton.contains(event.target) && dropdown && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});


document.addEventListener('DOMContentLoaded', function () {
    const taskCheckboxes = document.querySelectorAll('.task-checkbox');

    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const taskId = this.getAttribute('data-task-id');
            const label = document.querySelector(`label[for="task-${taskId}"]`);

            if (this.checked) {
                label.classList.add('line-through');
                console.log(`Task ${taskId} marked as completed`);
            } else {
                label.classList.remove('line-through');
                console.log(`Task ${taskId} marked as pending`);
            }
        });
    });

    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const sidebar = document.getElementById('sidebar');
    const body = document.body;
    
    if (mobileMenuButton && sidebar) {
        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            body.classList.toggle('mobile-menu-open');
        });
    }
 
    document.addEventListener('click', function(event) {
        if (sidebar && sidebar.classList.contains('open') && 
            !sidebar.contains(event.target) && 
            !mobileMenuButton.contains(event.target)) {
            sidebar.classList.remove('open');
            body.classList.remove('mobile-menu-open');
        }
    });
});

function handleResize() {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth > 640) {
        sidebar.classList.remove('open');
    }
}

window.addEventListener('resize', handleResize);