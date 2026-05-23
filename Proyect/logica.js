document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector(".form-card");
    

    if (form) {
        let inputCantidad, inputCosto, inputTotal, inputFecha;

        const fields = document.querySelectorAll(".field");

        fields.forEach(field => {
            const label = field.querySelector("label");
            const input = field.querySelector("input");
            
            if (label && input) {
                const textoLabel = label.textContent.trim().toUpperCase();
                
                // Asigna los inputs basándonos en el texto de su etiqueta
                if (textoLabel === "CANTIDAD") inputCantidad = input;
                if (textoLabel === "COSTO UNITARIO") inputCosto = input;
                if (textoLabel === "FECHA") inputFecha = input;
                if (textoLabel === "TOTAL") {
                    inputTotal = input;
                    inputTotal.readOnly = true; // Bloquea el Total para que no lo editen a mano
                }
            }
        });

        // Función para hacer la multiplicación automática
        const calcularTotal = () => {
            if (inputCantidad && inputCosto && inputTotal) {
                const cantidad = parseFloat(inputCantidad.value) || 0;
                const costo = parseFloat(inputCosto.value) || 0;
                inputTotal.value = (cantidad * costo).toFixed(2);
            }
        };

        // El sistema escucha cada vez que le pican un número
        if (inputCantidad && inputCosto) {
            inputCantidad.addEventListener("input", calcularTotal);
            inputCosto.addEventListener("input", calcularTotal);
        }

        // Para que no se recargue al darle al botón
        form.addEventListener("submit", (evento) => {
            evento.preventDefault(); 
            
            if (inputFecha && !inputFecha.value) {
                alert("⚠️ Error: El campo FECHA es obligatorio.");
                return;
            }

            console.log("¡Cálculo exitoso y formulario validado!");
            alert("¡Formulario listo! (Revisa la consola)");
        });
    }
});