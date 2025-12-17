// ========================================
// CALENDARIO DE PRESUPUESTOS - JavaScript
// ========================================

class CalendarioPresupuestos {
    constructor() {
        this.currentDate = new Date();
        this.currentMonth = this.currentDate.getMonth();
        this.currentYear = this.currentDate.getFullYear();
        this.today = new Date();

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
        document.getElementById('btnPrevMonth').addEventListener('click', () => this.previousMonth());
        document.getElementById('btnNextMonth').addEventListener('click', () => this.nextMonth());
        document.getElementById('btnToday').addEventListener('click', () => this.goToToday());
    }

    previousMonth() {
        this.currentMonth--;
        if (this.currentMonth < 0) { this.currentMonth = 11; this.currentYear--; }
        this.renderCalendar();
    }

    nextMonth() {
        this.currentMonth++;
        if (this.currentMonth > 11) { this.currentMonth = 0; this.currentYear++; }
        this.renderCalendar();
    }

    goToToday() {
        this.currentDate = new Date();
        this.currentMonth = this.currentDate.getMonth();
        this.currentYear = this.currentDate.getFullYear();
        this.renderCalendar();
    }

    renderCalendar() {
        document.getElementById('currentMonth').textContent = `${this.monthNames[this.currentMonth]} ${this.currentYear}`;
        const calendarDays = document.getElementById('calendarDays');
        calendarDays.innerHTML = '';

        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        let startingDayOfWeek = firstDay.getDay() - 1;
        if (startingDayOfWeek < 0) startingDayOfWeek = 6;

        const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        const daysInPrevMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
        const totalDays = 42;

        for (let i = 0; i < totalDays; i++) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('calendar-day');

            let dayNumber, isCurrentMonth = true, actualDate;

            if (i < startingDayOfWeek) {
                dayNumber = daysInPrevMonth - startingDayOfWeek + i + 1;
                dayElement.classList.add('other-month');
                isCurrentMonth = false;
                actualDate = new Date(this.currentYear, this.currentMonth - 1, dayNumber);
            } else if (i < startingDayOfWeek + daysInMonth) {
                dayNumber = i - startingDayOfWeek + 1;
                actualDate = new Date(this.currentYear, this.currentMonth, dayNumber);
                if (this.isToday(actualDate)) dayElement.classList.add('today');
                const dayOfWeek = actualDate.getDay();
                if (dayOfWeek === 0 || dayOfWeek === 6) dayElement.classList.add('weekend');
            } else {
                dayNumber = i - startingDayOfWeek - daysInMonth + 1;
                dayElement.classList.add('other-month');
                isCurrentMonth = false;
                actualDate = new Date(this.currentYear, this.currentMonth + 1, dayNumber);
            }

            dayElement.innerHTML = `
                <div class="day-number">${dayNumber}</div>
                <div class="day-events" data-date="${this.formatDate(actualDate)}"></div>
            `;

            dayElement.addEventListener('click', () => this.onDayClick(actualDate, isCurrentMonth));
            calendarDays.appendChild(dayElement);
        }

        this.loadPresupuestoEvents();
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

    loadPresupuestoEvents() {
        const formData = new FormData();
        formData.append('op', 'getMaintenanceEvents');
        formData.append('month', this.currentMonth + 1);
        formData.append('year', this.currentYear);

        fetch('../../controller/presupuesto.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') this.displayPresupuestoEvents(data.data);
            else console.error('Error en la respuesta:', data.message);
        })
        .catch(err => console.error('Error al cargar eventos:', err));
    }

    displayPresupuestoEvents(events) {
        if (!events || events.length === 0) return;

        events.forEach(evento => {
            const start = new Date(evento.fecha_inicio_evento_presupuesto);
            const end = new Date(evento.fecha_fin_evento_presupuesto);

            for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                const fechaStr = this.formatDate(d);
                const dayContainer = document.querySelector(`.day-events[data-date="${fechaStr}"]`);
                if (!dayContainer) continue;

                const eventoDiv = document.createElement('div');
                eventoDiv.classList.add('event-item');
                eventoDiv.style.backgroundColor = evento.color_estado_ppto;
                eventoDiv.style.color = '#fff';
                eventoDiv.textContent = evento.numero_presupuesto;
                eventoDiv.title = `${evento.numero_presupuesto} - ${evento.nombre_evento_presupuesto}\nCliente: ${evento.nombre_cliente}\nEstado: ${evento.nombre_estado_ppto}`;

                eventoDiv.addEventListener('click', e => {
                    e.stopPropagation();
                    this.showEventDetails(evento);
                });

                dayContainer.appendChild(eventoDiv);
                dayContainer.closest('.calendar-day')?.classList.add('has-events');
            }
        });
    }

    showEventDetails(evento) {
        document.getElementById('modalNumeroPresupuesto').textContent = evento.numero_presupuesto || '-';
        document.getElementById('modalClientePresupuesto').textContent = evento.nombre_cliente || '-';
        document.getElementById('modalNombreEvento').textContent = evento.nombre_evento_presupuesto || '-';
        document.getElementById('modalEstadoPresupuesto').textContent = evento.nombre_estado_ppto || '-';
        document.getElementById('modalImportePresupuesto').textContent = evento.total_presupuesto || '-';

        const modal = document.getElementById('modalDetalleElemento');
        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modal).show();
        } else if (typeof $?.fn?.modal !== 'undefined') {
            $('#modalDetalleElemento').modal('show');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const calendario = new CalendarioPresupuestos();
    window.calendario = calendario;
});
