const xValues = ["importación","Exportación"];
const yValues = [1200, 4500];
const barColors = ["blue","red"]

const ctx = document.getElementById('chart');

new Chart(ctx, {
    type: "bar",
    data: {
        labels: xValues,
        datasets: [{
            backgroundColor: barColors,
            data: yValues
        }]
    },
    options: {
        plugins:{
            legend: {display: false},
            title: {
                display: true,
                Text: "FACTURAS TOTALES EN IMPORTACIONES Y EXPORTACIONES",
                font:{size:18}
            }
        }
    }
});