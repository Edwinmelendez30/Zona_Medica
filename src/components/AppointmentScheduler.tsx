import React, { useState, useEffect } from 'react';
import DatePicker from 'react-datepicker';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { Calendar, Clock, User, MapPin, Phone } from 'lucide-react';
import toast from 'react-hot-toast';
import type { Doctor, Schedule, Appointment } from '../types/appointment';
import "react-datepicker/dist/react-datepicker.css";

interface Props {
  doctor: Doctor;
}

export const AppointmentScheduler: React.FC<Props> = ({ doctor }) => {
  const [selectedDate, setSelectedDate] = useState<Date | null>(null);
  const [selectedTime, setSelectedTime] = useState<string>('');
  const [reason, setReason] = useState('');
  const [availableTimes, setAvailableTimes] = useState<string[]>([]);
  const [schedule, setSchedule] = useState<Schedule[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchDoctorSchedule();
  }, [doctor.id]);

  useEffect(() => {
    if (selectedDate) {
      generateAvailableTimes();
    }
  }, [selectedDate, schedule]);

  const fetchDoctorSchedule = async () => {
    try {
      const response = await fetch(`/api/doctors/${doctor.id}/schedule`);
      const data = await response.json();
      setSchedule(data);
    } catch (error) {
      console.error('Error fetching schedule:', error);
      toast.error('Error al cargar el horario del médico');
    }
  };

  const generateAvailableTimes = async () => {
    if (!selectedDate) return;

    const dayOfWeek = format(selectedDate, 'EEEE', { locale: es });
    const daySchedule = schedule.find(s => s.dia_semana === dayOfWeek);

    if (!daySchedule) {
      setAvailableTimes([]);
      return;
    }

    try {
      const response = await fetch(`/api/doctors/${doctor.id}/available-times`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          date: format(selectedDate, 'yyyy-MM-dd'),
          scheduleId: daySchedule.id
        })
      });
      
      const times = await response.json();
      setAvailableTimes(times);
    } catch (error) {
      console.error('Error fetching available times:', error);
      toast.error('Error al cargar los horarios disponibles');
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedDate || !selectedTime || !reason) {
      toast.error('Por favor complete todos los campos');
      return;
    }

    setLoading(true);
    try {
      const response = await fetch('/api/appointments', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          medico_id: doctor.id,
          fecha: format(selectedDate, 'yyyy-MM-dd'),
          hora_inicio: selectedTime,
          motivo_consulta: reason
        })
      });

      if (!response.ok) throw new Error('Error al agendar la cita');
      
      toast.success('¡Cita agendada exitosamente!');
      setSelectedDate(null);
      setSelectedTime('');
      setReason('');
    } catch (error) {
      console.error('Error scheduling appointment:', error);
      toast.error('Error al agendar la cita');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto">
      <div className="flex items-center space-x-4 mb-6">
        <img 
          src={doctor.foto} 
          alt={doctor.nombre}
          className="w-16 h-16 rounded-full object-cover"
        />
        <div>
          <h2 className="text-xl font-semibold text-gray-800">{doctor.nombre}</h2>
          <p className="text-gray-600">{doctor.profesion}</p>
        </div>
      </div>

      <div className="space-y-2 mb-6">
        <div className="flex items-center text-gray-600">
          <MapPin className="w-5 h-5 mr-2" />
          <span>{doctor.ubicacion}</span>
        </div>
        <div className="flex items-center text-gray-600">
          <Phone className="w-5 h-5 mr-2" />
          <span>{doctor.telefono}</span>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Fecha de la cita
          </label>
          <div className="relative">
            <DatePicker
              selected={selectedDate}
              onChange={(date) => setSelectedDate(date)}
              minDate={new Date()}
              locale={es}
              dateFormat="dd/MM/yyyy"
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              placeholderText="Seleccione una fecha"
            />
            <Calendar className="absolute right-3 top-2.5 h-5 w-5 text-gray-400" />
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Hora de la cita
          </label>
          <div className="relative">
            <select
              value={selectedTime}
              onChange={(e) => setSelectedTime(e.target.value)}
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              disabled={!selectedDate || availableTimes.length === 0}
            >
              <option value="">Seleccione una hora</option>
              {availableTimes.map((time) => (
                <option key={time} value={time}>{time}</option>
              ))}
            </select>
            <Clock className="absolute right-3 top-2.5 h-5 w-5 text-gray-400" />
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Motivo de la consulta
          </label>
          <textarea
            value={reason}
            onChange={(e) => setReason(e.target.value)}
            className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            rows={3}
            placeholder="Describa brevemente el motivo de su consulta"
          />
        </div>

        <button
          type="submit"
          disabled={loading || !selectedDate || !selectedTime || !reason}
          className={`w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ${
            loading ? 'opacity-75 cursor-not-allowed' : ''
          }`}
        >
          {loading ? 'Agendando...' : 'Agendar Cita'}
        </button>
      </form>
    </div>
  );
};