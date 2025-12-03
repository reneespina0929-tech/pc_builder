


    document.getElementById("builder-btn-hero").addEventListener("click", () => {
        window.location.href = "../pages/system-builder.php";
    });

    document.getElementById("build-btn").addEventListener("click", () => {
        window.location.href = "../pages/system-builder.php";
    });

	document.getElementById("pre-built-btn").addEventListener("click", () => {
       window.location.href = "../pages/pre-built.php";
    });
	
    const componentsLink = document.getElementById('componentsLink');
        const componentsDropdown = document.getElementById('componentsDropdown');

        componentsLink.addEventListener('click', function(e) {
            e.preventDefault();
            componentsDropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!componentsLink.contains(e.target) && !componentsDropdown.contains(e.target)) {
                componentsDropdown.classList.remove('show');
            }
        });
