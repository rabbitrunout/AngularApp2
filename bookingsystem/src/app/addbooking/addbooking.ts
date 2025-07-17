import { Component, OnInit } from '@angular/core';
import { NgForm, FormsModule } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { BookingItem } from '../bookingItem';
import { BookingService } from '../booking.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ChangeDetectorRef } from '@angular/core';


@Component({
  selector: 'app-addbooking',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule, RouterModule ],
  templateUrl: './addbooking.html',
  styleUrls: ['./addbooking.css']
})
export class Addbooking {
  reservation: any = {};
  isEditing = false;
  success = '';
  error = '';
  selectedFile: File | null = null;

  constructor(private reservationService: BookingService) {}

  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0] ?? null;
  }

  resetAlerts() {
    this.success = '';
    this.error = '';
  }

  getReservations() {
    // сюда можно добавить логику обновления списка, если нужно
  }

  addReservation(form: NgForm): void {
    this.resetAlerts();

    const isEdit = !!this.reservation.ID;

    if (!this.reservation.location || !this.reservation.start_time || !this.reservation.end_time) {
      this.error = 'Please fill in all required fields.';
      return;
    }

    if (isEdit) {
      this.reservationService.edit(this.reservation).subscribe({
        next: () => {
          this.success = 'Reservation updated successfully';
          this.getReservations();
          this.resetForm(form);
        },
        error: () => (this.error = 'Error updating reservation')
      });
    } else {
      const formData = new FormData();
      formData.append('location', this.reservation.location);
      formData.append('start_time', this.reservation.start_time);
      formData.append('end_time', this.reservation.end_time);
      formData.append('complete', this.reservation.complete ? '1' : '0');
      if (this.selectedFile) {
        formData.append('image', this.selectedFile);
      }

      this.reservationService.add(formData).subscribe({
        next: () => {
          this.success = 'Reservation added successfully';
          this.getReservations();
          this.resetForm(form);
        },
        error: () => (this.error = 'Error creating reservation')
      });
    }
  }

  resetForm(form: NgForm) {
    form.resetForm();
    this.isEditing = false;
    this.reservation = {};
    this.success = '';
    this.error = '';
    this.selectedFile = null;
  }
}
