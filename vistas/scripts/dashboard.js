var chartActividad = null;

$(document).ready(function() {
    cargarEstadisticas();
    cargarActividadReciente();
    cargarEstadisticasPorMes();
});

function cargarEstadisticas() {
    $.get('../ajax/dashboard.php?op=estadisticas', function(data) {
        try {
            var stats = typeof data === 'string' ? JSON.parse(data) : data;
            
            // Actualizar tarjetas principales
            $('#stat-cot-total').text(stats.cotizaciones_total || 0);
            $('#stat-con-total').text(stats.contratos_total || 0);
            $('#stat-usuarios').text(stats.usuarios_activos || 0);
            
            var montoTotal = parseFloat(stats.cotizaciones_monto_total || 0) + parseFloat(stats.contratos_monto_total || 0);
            $('#stat-monto-total').text('Bs. ' + formatearNumero(montoTotal));
            
            // Actualizar estadísticas detalladas de cotizaciones
            $('#stat-cot-vigentes').text(stats.cotizaciones_vigentes || 0);
            $('#stat-cot-anuladas').text(stats.cotizaciones_anuladas || 0);
            $('#stat-cot-monto').text('Bs. ' + formatearNumero(stats.cotizaciones_monto_total || 0));
            
            // Actualizar estadísticas detalladas de contratos
            $('#stat-con-vigentes').text(stats.contratos_vigentes || 0);
            $('#stat-con-anulados').text(stats.contratos_anulados || 0);
            $('#stat-con-monto').text('Bs. ' + formatearNumero(stats.contratos_monto_total || 0));
            
            // Actualizar módulos
            $('#stat-fichas').text(stats.fichas_recojo || 0);
            $('#stat-itinerarios').text(stats.itinerarios || 0);
            $('#stat-documentos').text(stats.documentos_registro || 0);
            
        } catch (e) {
            console.error('Error al parsear estadísticas:', e);
        }
    }).fail(function(xhr, status, error) {
        console.error('Error al cargar estadísticas:', error);
    });
}

function cargarActividadReciente() {
    $.get('../ajax/dashboard.php?op=actividadReciente&limite=10', function(data) {
        try {
            var actividades = typeof data === 'string' ? JSON.parse(data) : data;
            var lista = $('#lista-actividad');
            lista.empty();
            
            if (actividades.length === 0) {
                lista.html('<li class="item"><div class="product-info"><p class="text-muted text-center">No hay actividad reciente</p></div></li>');
                return;
            }
            
            actividades.forEach(function(act) {
                var icono = act.tipo === 'cotizacion' ? 'fa-file-pdf-o' : 'fa-file-text-o';
                var color = act.tipo === 'cotizacion' ? 'aqua' : 'green';
                var estadoClass = act.estado === 'vigente' ? 'label-success' : 'label-danger';
                var fecha = new Date(act.fecha_creacion);
                var fechaFormateada = fecha.toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' });
                
                var html = '<li class="item">';
                html += '<div class="product-img">';
                html += '<i class="fa ' + icono + ' fa-2x text-' + color + '"></i>';
                html += '</div>';
                html += '<div class="product-info">';
                html += '<a href="#" class="product-title">' + act.nombre;
                html += '<span class="label ' + estadoClass + ' pull-right">' + act.estado + '</span>';
                html += '</a>';
                html += '<span class="product-description">';
                html += 'Código: ' + act.codigo + ' | Monto: Bs. ' + act.total;
                html += '<br><small class="text-muted">' + fechaFormateada + '</small>';
                html += '</span>';
                html += '</div>';
                html += '</li>';
                
                lista.append(html);
            });
            
        } catch (e) {
            console.error('Error al parsear actividad:', e);
            $('#lista-actividad').html('<li class="item"><div class="product-info"><p class="text-danger text-center">Error al cargar actividad</p></div></li>');
        }
    }).fail(function(xhr, status, error) {
        console.error('Error al cargar actividad reciente:', error);
        $('#lista-actividad').html('<li class="item"><div class="product-info"><p class="text-danger text-center">Error al cargar actividad</p></div></li>');
    });
}

function cargarEstadisticasPorMes() {
    $.get('../ajax/dashboard.php?op=estadisticasPorMes', function(data) {
        try {
            var datos = typeof data === 'string' ? JSON.parse(data) : data;
            
            var meses = [];
            var cotizaciones = [];
            var contratos = [];
            var cotizacionesMonto = [];
            var contratosMonto = [];
            
            datos.forEach(function(item) {
                meses.push(item.mes);
                cotizaciones.push(item.cotizaciones);
                contratos.push(item.contratos);
                cotizacionesMonto.push(item.cotizaciones_monto);
                contratosMonto.push(item.contratos_monto);
            });
            
            // Destruir gráfico anterior si existe
            if (chartActividad) {
                chartActividad.destroy();
            }
            
            var ctx = document.getElementById('chartActividad').getContext('2d');
            chartActividad = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: meses,
                    datasets: [
                        {
                            label: 'Cotizaciones',
                            data: cotizaciones,
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Contratos',
                            data: contratos,
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            });
            
        } catch (e) {
            console.error('Error al parsear datos por mes:', e);
        }
    }).fail(function(xhr, status, error) {
        console.error('Error al cargar estadísticas por mes:', error);
    });
}

function formatearNumero(numero) {
    return parseFloat(numero).toLocaleString('es-BO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}








