// ========================================
// CALENDARIO DE MANTENIMIENTOS - JavaScript
// ========================================

class CalendarioMantenimientos {
    constructor() {
        this.currentDate = new Date();
        this.currentMonth = this.currentDate.getMonth();
        this.currentYear = this.currentDate.getFullYear();
        this.today = new Date();
        
        // Array con nombres de meses
        this.monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        
        this.init();
    }
    
    init() {
        this.attachEventListeners();
        this.renderCalendar();
    }
    
    attachEventListeners() {
        // Botón mes anterior
        document.getElementById('btnPrevMonth').addEventListener('click', () => {
            this.previousMonth();
        });
        
        // Botón mes siguiente
        document.getElementById('btnNextMonth').addEventListener('click', () => {
            this.nextMonth();
        });
        
        // Botón hoy
        document.getElementById('btnToday').addEventListener('click', () => {
            this.goToToday();
        });
    }
    
    previousMonth() {
        this.currentMonth--;
        if (this.currentMonth < 0) {
            this.currentMonth = 11;
            this.currentYear--;
        }
        this.renderCalendar();
    }
    
    nextMonth() {
        this.currentMonth++;
        if (this.currentMonth > 11) {
            this.currentMonth = 0;
            this.currentYear++;
        }
        this.renderCalendar();
    }
    
    goToToday() {
        this.currentDate = new Date();
        this.currentMonth = this.currentDate.getMonth();
        this.currentYear = this.currentDate.getFullYear();
        this.renderCalendar();
    }
    
    renderCalendar() {
        // Actualizar cabecera con mes y año
        document.getElementById('currentMonth').textContent = 
            `${this.monthNames[this.currentMonth]} ${this.currentYear}`;
        
        // Limpiar días previos
        const calendarDays = document.getElementById('calendarDays');
        calendarDays.innerHTML = '';
        
        // Obtener primer día del mes (0 = Domingo, 1 = Lunes, etc.)
        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        // Ajustar para que Lunes sea 0
        let startingDayOfWeek = firstDay.getDay() - 1;
        if (startingDayOfWeek < 0) startingDayOfWeek = 6;
        
        // Obtener número de días del mes
        const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        
        // Obtener días del mes anterior
        const daysInPrevMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
        
        // Calcular días totales a mostrar (siempre 42 para grid completo)
        const totalDays = 42;
        
        // Renderizar días
        for (let i = 0; i < totalDays; i++) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('calendar-day');
            
            let dayNumber;
            let isCurrentMonth = true;
            let actualDate;
            
            // Días del mes anterior
            if (i < startingDayOfWeek) {
                dayNumber = daysInPrevMonth - startingDayOfWeek + i + 1;
                dayElement.classList.add('other-month');
                isCurrentMonth = false;
                actualDate = new Date(this.currentYear, this.currentMonth - 1, dayNumber);
            }
            // Días del mes actual
            else if (i < startingDayOfWeek + daysInMonth) {
                dayNumber = i - startingDayOfWeek + 1;
                actualDate = new Date(this.currentYear, this.currentMonth, dayNumber);
                
                // Marcar día actual
                if (this.isToday(actualDate)) {
                    dayElement.classList.add('today');
                }
                
                // Marcar fin de semana
                const dayOfWeek = actualDate.getDay();
                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    dayElement.classList.add('weekend');
                }
            }
            // Días del mes siguiente
            else {
                dayNumber = i - startingDayOfWeek - daysInMonth + 1;
                dayElement.classList.add('other-month');
                isCurrentMonth = false;
                actualDate = new Date(this.currentYear, this.currentMonth + 1, dayNumber);
            }
            
            // Construir HTML del día
            dayElement.innerHTML = `
                <div class="day-number">${dayNumber}</div>
                <div class="day-events" data-date="${this.formatDate(actualDate)}">
                    <!-- Aquí se cargarán los eventos de mantenimientos -->
                </div>
            `;
            
            // Evento click en el día
            dayElement.addEventListener('click', () => {
                this.onDayClick(actualDate, isCurrentMonth);
            });
            
            calendarDays.appendChild(dayElement);
        }
        
        // Cargar eventos de mantenimientos
        this.loadMaintenanceEvents();
    }
    
    isToday(date) {
        return date.getDate() === this.today.getDate() &&
               date.getMonth() === this.today.getMonth() &&
               date.getFullYear() === this.today.getFullYear();
    }
    
    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    onDayClick(date, isCurrentMonth) {
        console.log('Día seleccionado:', this.formatDate(date));
        console.log('Es del mes actual:', isCurrentMonth);
    }
    
    // Método para cargar eventos de mantenimientos
    loadMaintenanceEvents() {
        console.log('Cargando eventos de mantenimientos...');
        
        const formData = new FormData();
        formData.append('op', 'getMaintenanceEvents');
        formData.append('month', this.currentMonth + 1);
        formData.append('year', this.currentYear);
        
        fetch('../../controller/elemento.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);
            if (data.status === 'success') {
                this.displayMaintenanceEvents(data.data);
            } else {
                console.error('Error en la respuesta:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al cargar eventos:', error);
        });
    }
    
    // Método para mostrar eventos en el calendario
    displayMaintenanceEvents(events) {
        console.log('Mostrando eventos:', events);
        
        if (!events || events.length === 0) {
            console.log('No hay eventos de mantenimientos para este mes');
            return;
        }
        
        // Recorrer cada evento
        events.forEach(evento => {
            const fechaMantenimiento = evento.proximo_mantenimiento_elemento;
            const estadoMantenimiento = evento.estado_mantenimiento_elemento;
            const codigoElemento = evento.codigo_elemento;
            
            // Buscar el contenedor del día correspondiente
            const dayContainer = document.querySelector(`.day-events[data-date="${fechaMantenimiento}"]`);
            
            if (dayContainer) {
                // Determinar el color según el estado
                let colorClass = '';
                let bgColor = '';
                
                switch(estadoMantenimiento) {
                    case 'Atrasado':
                        colorClass = 'bg-danger';
                        bgColor = '#dc3545';
                        break;
                    case 'Próximo':
                        colorClass = 'bg-warning';
                        bgColor = '#ffc107';
                        break;
                    case 'Al día':
                        colorClass = 'bg-success';
                        bgColor = '#198754';
                        break;
                    default:
                        colorClass = 'bg-secondary';
                        bgColor = '#6c757d';
                }
                
                // Crear el elemento visual
                const eventoElement = document.createElement('div');
                eventoElement.className = `event-item ${colorClass} text-white`;
                eventoElement.style.cssText = `
                    padding: 1px 3px;
                    margin: 1px 0;
                    border-radius: 2px;
                    font-size: 0.65rem;
                    cursor: pointer;
                    background-color: ${bgColor};
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    line-height: 1.3;
                `;
                eventoElement.textContent = codigoElemento;
                eventoElement.title = `${codigoElemento} - ${evento.nombre_articulo}\n${evento.descripcion_elemento}\nEstado: ${estadoMantenimiento}`;
                
                // Evento click para mostrar detalles
                eventoElement.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.showEventDetails(evento);
                });
                
                // Agregar al contenedor del día
                dayContainer.appendChild(eventoElement);
                
                // Marcar el día con clase especial
                const dayElement = dayContainer.closest('.calendar-day');
                if (dayElement) {
                    if (estadoMantenimiento === 'Atrasado') {
                        dayElement.classList.add('has-expired');
                    } else {
                        dayElement.classList.add('has-events');
                    }
                }
            }
        });
    }
    
    // Método para mostrar detalles del evento
    showEventDetails(evento) {
        // Rellenar datos básicos del elemento
        document.getElementById('modalCodigoElemento').textContent = evento.codigo_elemento || '-';
        document.getElementById('modalDescripcionElemento').textContent = evento.descripcion_elemento || '-';
        
        // Rellenar datos del artículo y familia
        document.getElementById('modalNombreArticulo').textContent = evento.nombre_articulo || '-';
        document.getElementById('modalNombreFamilia').textContent = evento.nombre_familia || '-';
        document.getElementById('modalNombreMarca').textContent = evento.nombre_marca || '-';
        document.getElementById('modalNombreGrupo').textContent = evento.nombre_grupo || '-';
        
        // Rellenar datos de mantenimiento
        document.getElementById('modalFechaMantenimiento').textContent = evento.proximo_mantenimiento_elemento || '-';
        
        // Estado de mantenimiento con badge de color
        const estadoElement = document.getElementById('modalEstadoMantenimiento');
        let badgeClass = 'badge-secondary';
        switch(evento.estado_mantenimiento_elemento) {
            case 'Atrasado':
                badgeClass = 'badge-danger';
                break;
            case 'Próximo':
                badgeClass = 'badge-warning';
                break;
            case 'Al día':
                badgeClass = 'badge-success';
                break;
        }
        estadoElement.innerHTML = `<span class="badge ${badgeClass}">${evento.estado_mantenimiento_elemento || '-'}</span>`;
        
        // Rellenar ubicación
        const nave = evento.nave_elemento;
        const pasillo = evento.pasillo_columna_elemento;
        const altura = evento.altura_elemento;
        
        if (nave || pasillo || altura) {
            document.getElementById('modalNave').textContent = nave || '-';
            document.getElementById('modalPasillo').textContent = pasillo || '-';
            document.getElementById('modalAltura').textContent = altura || '-';
            document.getElementById('modalNaveContainer').style.display = 'block';
            document.getElementById('modalPasilloContainer').style.display = 'block';
            document.getElementById('modalAlturaContainer').style.display = 'block';
        } else {
            document.getElementById('modalNaveContainer').style.display = 'none';
            document.getElementById('modalPasilloContainer').style.display = 'none';
            document.getElementById('modalAlturaContainer').style.display = 'none';
        }
        
        // Rellenar información adicional
        const modelo = evento.modelo_elemento;
        const serie = evento.numero_serie_elemento;
        
        if (modelo || serie) {
            document.getElementById('modalModelo').textContent = modelo || '-';
            document.getElementById('modalSerie').textContent = serie || '-';
            document.getElementById('modalModeloContainer').style.display = 'block';
            document.getElementById('modalSerieContainer').style.display = 'block';
            document.getElementById('modalInfoAdicional').style.display = 'block';
        } else {
            document.getElementById('modalModeloContainer').style.display = 'none';
            document.getElementById('modalSerieContainer').style.display = 'none';
            document.getElementById('modalInfoAdicional').style.display = 'none';
        }
        
        // Mostrar el modal (compatible con Bootstrap 4 y 5)
        const modal = document.getElementById('modalDetalleElemento');
        if (typeof bootstrap !== 'undefined') {
            // Bootstrap 5
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } else if (typeof $.fn.modal !== 'undefined') {
            // Bootstrap 4 con jQuery
            $('#modalDetalleElemento').modal('show');
        }
        
        console.log('Detalles del evento:', evento);
    }
}

// Inicializar el calendario cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const calendario = new CalendarioMantenimientos();
    
    // Hacer disponible globalmente para debugging
    window.calendario = calendario;
});
