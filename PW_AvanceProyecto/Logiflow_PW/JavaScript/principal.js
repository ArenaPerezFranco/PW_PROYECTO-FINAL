function toggleSidenav() {
    const sidenav = document.getElementById("sidenav");
    sidenav.classList.toggle("min");
}

// APERTURA DE SUBMENUS
document.addEventListener("DOMContentLoaded", () => {
    const menuButtons = document.querySelectorAll(".menu-btn");

    menuButtons.forEach(button => {
        button.addEventListener("click", (e) => {
            e.preventDefault(); // Evita que la página salte al usar href="#"
            
            // Buscamos el submenú que está justo debajo de este botón
            const submenu = button.nextElementSibling;
            
            if (submenu && submenu.classList.contains("submenu-container")) {
                // Alternar la clase 'show' para abrir/cerrar
                submenu.classList.toggle("show");
            }
        });
    });
});