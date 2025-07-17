import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { NgForm, FormsModule } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';

import { BookingItem } from '../bookingItem';
import { BookingService } from '../booking.service';

@Component({
  standalone: true,
  selector: 'app-booking',
  imports: [CommonModule, FormsModule, HttpClientModule, RouterModule],
  providers: [BookingService],
  templateUrl: './booking.component.html',
  styleUrls: ['./booking.component.css']
})
export class BookingComponent implements OnInit {
  reservations: BookingItem[] = [];
  reservation: BookingItem = this.getEmptyReservation();
  selectedFile: File | null = null;

  success = '';
  error = '';
  isEditing = false;

  constructor(
    private reservationService: BookingService,
    private http: HttpClient,
    private cdr: ChangeDetectorRef,
    private router: Router
  ) {}

  goToEdit(id: number) {
    // Исправлено с 'updatebooking' на 'edit' — совпадает с маршрутом
    this.router.navigate(['/edit', id]);
  }

  ngOnInit(): void {
    this.getReservations();
  }

  getReservations(): void {
    this.reservationService.getAll().subscribe({
      next: (data) => {
        this.reservations = data;
        this.success = 'Reservations loaded successfully';
        this.error = '';
        this.cdr.detectChanges();
      },
      error: () => {
        this.error = 'Failed to load reservations';
        this.success = '';
      }
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files?.length) {
      this.selectedFile = input.files[0];
      this.error = '';
      this.success = '';
    }
  }

  addReservation(form: NgForm): void {
    this.resetAlerts();

    const isEdit = !!this.reservation.ID;

    if (!this.reservation.location || !this.reservation.start_time || !this.reservation.end_time) {
      this.error = 'Please fill in all required fields.';
      return;
    }

    const formData = new FormData();
    if (isEdit) {
      formData.append('ID', this.reservation.ID.toString());
    }
    formData.append('location', this.reservation.location);
    formData.append('start_time', this.reservation.start_time);
    formData.append('end_time', this.reservation.end_time);
    formData.append('complete', this.reservation.complete ? '1' : '0');

    if (this.selectedFile) {
      formData.append('image', this.selectedFile);
    }

    const action$ = isEdit ? this.reservationService.edit(formData) : this.reservationService.add(formData);

    action$.subscribe({
      next: () => {
        this.success = isEdit ? 'Reservation updated successfully' : 'Reservation added successfully';
        this.error = '';
        this.getReservations();
        this.resetForm(form);
      },
      error: () => {
        this.error = isEdit ? 'Error updating reservation' : 'Error creating reservation';
        this.success = '';
      }
    });
  }

  editReservation(item: BookingItem): void {
    this.reservation = { ...item };
    this.isEditing = true;
    this.error = '';
    this.success = '';
  }

  deleteReservation(ID?: number): void {
    if (!ID) return;
    this.reservationService.delete(ID).subscribe({
      next: () => {
        this.success = 'Deleted successfully';
        this.error = '';
        this.getReservations();
      },
      error: () => {
        this.error = 'Failed to delete reservation';
        this.success = '';
      }
    });
  }

  resetForm(form?: NgForm): void {
    this.reservation = this.getEmptyReservation();
    this.selectedFile = null;
    this.isEditing = false;
    this.resetAlerts();
    form?.resetForm();
  }

  resetAlerts(): void {
    this.success = '';
    this.error = '';
  }

  private getEmptyReservation(): BookingItem {
    return {
      ID: 0,
      location: '',
      start_time: '',
      end_time: '',
      complete: false,
      imageName: ''
    };
  }
}
