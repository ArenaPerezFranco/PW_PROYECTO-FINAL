document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector(".form-card");

    if (form) {
        // Variables para cálcular
        let inputCantidad, inputCosto, inputTotal;
        
        // Variables para validación obligatoria
        let inputFechaImpo, inputNumFactura, inputFechaFactura;

        const fields = document.querySelectorAll(".field");

        fields.forEach(field => {
            const label = field.querySelector("label");
            const input = field.querySelector("input") || field.querySelector("select");
            
            if (label && input) {
                const textoLabel = label.textContent.trim().toUpperCase();
                
                // Mapeo dinámico para matemáticas
                if (textoLabel === "CANTIDAD") inputCantidad = input;
                if (textoLabel === "COSTO UNITARIO") inputCosto = input;
                if (textoLabel === "TOTAL") {
                    inputTotal = input;
                    inputTotal.readOnly = true; 
                }

                // Mapeo para validaciones
                if (textoLabel === "FECHA IMPO" || textoLabel === "FECHA EXPO") inputFechaImpo = input;
                if (textoLabel === "NÚMERO DE FACTURA") inputNumFactura = input;
                if (textoLabel === "FECHA DE FACTURA") inputFechaFactura = input;
            }
        });

        // Autocalcular el Total
        const calcularTotal = () => {
            if (inputCantidad && inputCosto && inputTotal) {
                const cantidad = parseFloat(inputCantidad.value) || 0;
                const costo = parseFloat(inputCosto.value) || 0;
                inputTotal.value = (cantidad * costo).toFixed(2);
            }
        };

        if (inputCantidad && inputCosto) {
            inputCantidad.addEventListener("input", calcularTotal);
            inputCosto.addEventListener("input", calcularTotal);
        }

        // Validación final antes de permitir que PHP inserte datos
        form.addEventListener("submit", (evento) => {
            if (inputFechaImpo && !inputFechaImpo.value) {
                evento.preventDefault();
                alert("⚠️ Error: La fecha de la operación es obligatoria.");
                return;
            }
            if (inputNumFactura && !inputNumFactura.value) {
                evento.preventDefault();
                alert("⚠️ Error: Debes ingresar el Número de Factura.");
                return;
            }
        });
    }
});