export interface Doctor {
  id: number;
  nombre: string;
  email: string;
  profesion: string;
  ubicacion: string;
  telefono: string;
  foto: string;
  calificacion: number;
}

export interface Schedule {
  id: number;
  medico_id: number;
  dia_semana: string;
  hora_inicio: string;
  hora_fin: string;
  intervalo_citas: number;
}

export interface Appointment {
  id: number;
  paciente_id: number;
  medico_id: number;
  fecha: string;
  hora_inicio: string;
  hora_fin: string;
  motivo_consulta: string;
  estado: 'Pendiente' | 'Confirmada' | 'Cancelada' | 'Completada';
}