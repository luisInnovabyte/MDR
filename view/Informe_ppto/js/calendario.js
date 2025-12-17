// ========================================
// CALENDARIO DE MANTENIMIENTOS - JavaScript
// ========================================

class CalendarioPresupuestos {
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
        console.log('Cargando eventos de presupuestos...');
        
        const formData = new FormData();
        formData.append('op', 'getMaintenanceEvents');
        formData.append('month', this.currentMonth + 1);
        formData.append('year', this.currentYear);
        
        fetch('../../controller/presupuesto.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);
            if (data.data && Array.isArray(data.data)) {
                this.displayMaintenanceEvents(data.data);
            } else {
        console.error('No hay datos válidos en data.data');
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
        events.forEach(p => {
            const startDate = p.fecha_inicio_evento_presupuesto;
            const endDate = p.fecha_fin_evento_presupuesto;
            
              // Recorremos cada día del evento
        for (
            let d = new Date(startDate);
            d <= endDate;
            d.setDate(d.getDate() + 1)
        ) {

            const fecha = this.formatDate(d);
            const dayContainer = document.querySelector(
                `.day-events[data-date="${fecha}"]`
            );

            if (!dayContainer) continue;

            // Crear evento visual
            const eventoDiv = document.createElement('div');
            eventoDiv.classList.add('event-item');
            eventoDiv.style.backgroundColor = p.color_estado_ppto;
            eventoDiv.style.color = '#fff';

            eventoDiv.textContent = p.numero_presupuesto;
            eventoDiv.title = `
            ${p.numero_presupuesto}
            ${p.nombre_evento_presupuesto}
            ${p.nombre_cliente}
            Estado: ${p.nombre_estado_ppto}
                        `;

            eventoDiv.addEventListener('click', e => {
                e.stopPropagation();
                this.showEventDetails(p);
            });

            dayContainer.appendChild(eventoDiv);

            // Marcar el día como que tiene eventos
            dayContainer.closest('.calendar-day')
                ?.classList.add('has-events');
        }
            
            
        
            
    
        });
    }
    // Método para mostrar detalles del presupuesto
showEventDetails(evento) {
    // Rellenar datos del modal de presupuesto
    document.getElementById('modalNumeroPresupuesto').textContent = evento.numero_presupuesto || '-';
    document.getElementById('modalClientePresupuesto').textContent = evento.nombre_cliente || '-';
    document.getElementById('modalNombreEvento').textContent = evento.nombre_evento_presupuesto || '-';
    document.getElementById('modalEstadoPresupuesto').textContent = evento.nombre_estado_ppto || '-';
    document.getElementById('modalImportePresupuesto').textContent = evento.total_presupuesto || '-';

    // Mostrar el modal (Bootstrap 4 y 5)
    const modal = document.getElementById('modalDetalleElemento');
    if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } else if (typeof $.fn.modal !== 'undefined') {
        $('#modalDetalleElemento').modal('show');
    }

    console.log('Detalles del presupuesto:', evento);
}

    
}


// Inicializar el calendario cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const calendario = new CalendarioPresupuestos();
    
    // Hacer disponible globalmente para debugging
    window.calendario = calendario;
});
