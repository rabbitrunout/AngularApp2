import { Component, OnInit } from '@angular/core';
import { NgForm, FormsModule } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { BookingItem } from '../bookingItem';
import { BookingService } from '../booking.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ChangeDetectorRef } from '@angular/core';

@Component({
  standalone: true,
  selector: 'app-booking',
  imports: [HttpClientModule, CommonModule, FormsModule, RouterModule],
  providers: [BookingService],
  templateUrl: './booking.component.html',
  styleUrls: ['./booking.component.css']
})
export class BookingComponent implements OnInit {
  reservations: BookingItem[] = [];
  reservation: BookingItem = {
    ID: 0,
    location: '',
    start_time: '',
    end_time: '',
    complete: false,
    imageName: ''
  };

  selectedFile: File | null = null;
  success = '';
  error = '';
  isEditing = false;

  constructor(
    private reservationService: BookingService,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.getReservations();
  }

  getReservations(): void {
    this.reservationService.getAll().subscribe({
      next: (data) => {
        this.reservations = data;
        this.success = 'Reservations loaded successfully';
        this.cdr.detectChanges();
      },
      error: () => {
        this.error = 'Failed to load reservations';
      }
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files?.length) {
      this.selectedFile = input.files[0];
    }
  }

  addReservation(form: NgForm): void {
    this.resetAlerts();

    const isEdit = !!this.reservation.ID;

    const proceed = () => {
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
    };

    if (!this.reservation.location || !this.reservation.start_time || !this.reservation.end_time) {
      this.error = 'Please fill in all required fields.';
      return;
    }

    if (isEdit || !this.selectedFile) {
      proceed();
    } else {
      const uploadFormData = new FormData();
      uploadFormData.append('image', this.selectedFile);
      this.http.post<any>('http://localhost/angularapp2/bookingapi/upload.php', uploadFormData).subscribe({
        next: (res) => {
          this.reservation.imageName = res?.fileName || '';
          proceed();
        },
        error: () => {
          this.error = 'Image upload failed';
          this.reservation.imageName = '';
          proceed(); // continue anyway
        }
      });
    }
  }

  editReservation(item: BookingItem): void {
    this.reservation = { ...item };
    this.isEditing = true;
  }

  deleteReservation(ID?: number): void {
    if (!ID) return;
    this.reservationService.delete(ID).subscribe({
      next: () => {
        this.success = 'Deleted successfully';
        this.getReservations();
      },
      error: () => (this.error = 'Failed to delete reservation')
    });
  }

  resetForm(f?: NgForm): void {
    this.reservation = {
      ID: 0,
      location: '',
      start_time: '',
      end_time: '',
      complete: false,
      imageName: ''
    };
    this.selectedFile = null;
    this.isEditing = false;
    this.success = '';
    this.error = '';
    f?.resetForm(); // сброс формы Angular
  }

  resetAlerts(): void {
    this.error = '';
    this.success = '';
  }
}
