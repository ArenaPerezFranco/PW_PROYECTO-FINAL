const xValues = ["importación","Exportación"];
const yValues = [1200, 4500]; //Totales de BD en MySQL

//COLORES DE LAS BARRAS
const barColors = ["rgba(30, 99, 146, 0.8)","rgba(197, 104, 197, 0.8)" ];
const borderColors = ["rgba(30, 99, 146, 1)","rgba(197, 104, 197, 1)"];

const ctx = document.getElementById('chart');

new Chart(ctx, {
    type: "bar",
    data: {
        labels: xValues,
        datasets: [{
            backgroundColor: barColors,
            borderColor: borderColors,
            borderWidth: 1.5,
            borderRadius: 6,
            borderSkipped: false,
            data: yValues
        }]
    },
    options: {
        responsive: true,
        //maintainAspectRatio:false,
        plugins:{
            legend: {
                display: false
            },
            title: {
                display: true,
                Text: "FACTURAS TOTALES EN IMPORTACIONES Y EXPORTACIONES",
                font:{
                    size:18,
                    family: "'Segoe UI, sans-serif'",
                    weight: 'bold'
                },
                color: '#2c3e50',
                padding: {
                    bottom: 20
                }
            }
        },
        //ESCALAS DE X y Y
        scales: {
            y:{
                beginAtZero: true,
                ticks:{
                    //Aumentar el tamaño de fuente de ejes
                    //para que no sea pequeño en grafica grande
                    font:{
                        size: 14
                    }
                }
            },
            x:{
                display: false,
                ticks:{
                    font:{
                        size: 14,
                        weight:'600'
                    }
                }
            },
        }
    }
});