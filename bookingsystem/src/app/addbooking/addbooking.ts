import { Component } from '@angular/core';
import { NgForm, FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { BookingService } from '../booking.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-addbooking',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule, RouterModule],
  templateUrl: './addbooking.html',
  styleUrls: ['./addbooking.css']
})
export class Addbooking {
  reservation: any = {
    complete: false
  };

  isEditing = false;
  success = '';
  error = '';
  selectedFile: File | null = null;

  constructor(private reservationService: BookingService) {}

  onFileSelected(event: any) {
    const file: File = event.target.files[0];
    if (file) {
      this.selectedFile = file;
    }
  }

  resetAlerts() {
    this.success = '';
    this.error = '';
  }

  getReservations() {
    // Можно обновить список, если нужно
  }

  addReservation(form: NgForm): void {
    this.resetAlerts();

    const isEdit = !!this.reservation.ID;

    // Проверка на обязательные поля
    if (!this.reservation.location || !this.reservation.start_time || !this.reservation.end_time) {
      this.error = 'Please fill in all required fields.';
      return;
    }

    // Для редактирования (POST с JSON, не FormData)
    if (isEdit) {
      this.reservationService.edit(this.reservation).subscribe({
        next: () => {
          this.success = 'Reservation updated successfully';
          this.getReservations();
          this.resetForm(form);
        },
        error: (err) => {
          console.error(err);
          this.error = 'Error updating reservation';
        }
      });
    } else {
      // Добавление — используется FormData
      const formData = new FormData();
      formData.append('location', this.reservation.location);
      formData.append('start_time', this.reservation.start_time);
      formData.append('end_time', this.reservation.end_time);
      formData.append('complete', this.reservation.complete ? '1' : '0');

      if (this.selectedFile) {
        formData.append('image', this.selectedFile);
      }

      this.reservationService.add(formData).subscribe({
        next: (res) => {
          this.success = 'Reservation added successfully';
          console.log('Server response:', res);
          this.getReservations();
          this.resetForm(form);
        },
        error: (err) => {
          console.error('Error response:', err);
          if (err.status === 400) {
            this.error = 'Missing required fields or bad data format.';
          } else if (err.status === 409) {
            this.error = 'Duplicate reservation for the same time and location.';
          } else {
            this.error = 'Error creating reservation. Try again.';
          }
        }
      });
    }
  }

  resetForm(form: NgForm) {
    form.resetForm();
    this.reservation = { complete: false };
    this.isEditing = false;
    this.selectedFile = null;
    this.resetAlerts();
  }
}
