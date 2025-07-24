import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, NgForm } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { RouterModule, Router } from '@angular/router';

import { BookingItem } from '../bookingItem';
import { BookingService } from '../booking.service';
import { Auth } from '../services/auth';

@Component({
  standalone: true,
  selector: 'app-booking',
  imports: [HttpClientModule, CommonModule, FormsModule, RouterModule],
  providers: [BookingService],
  templateUrl: './booking.component.html',
  styleUrls: ['./booking.component.css'],
})
export class BookingComponent implements OnInit {
  public reservations: BookingItem[] = [];
  public bookings: BookingItem[] = [];
  reservation: BookingItem = this.getEmptyReservation();

  selectedFile: File | null = null;
  userName = '';

  success = '';
  error = '';
  isEditing = false;

  constructor(
    private reservationService: BookingService,
    private http: HttpClient,
    private cdr: ChangeDetectorRef,
    private router: Router,
    public auth: Auth
  ) {}

  ngOnInit(): void {
  this.getReservations();
  // this.loadBookings();
  this.userName = localStorage.getItem('username') || 'Guest';  // исправлено здесь
  this.cdr.detectChanges();
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
      },
    });
  }

  addReservation(form: NgForm): void {
    this.resetAlerts();

    if (!this.reservation.location || !this.reservation.start_time || !this.reservation.end_time) {
      this.error = 'Please fill in all required fields.';
      return;
    }

    const isEdit = !!this.reservation.ID;

    const formData = new FormData();
    if (isEdit) formData.append('ID', this.reservation.ID.toString());
    formData.append('location', this.reservation.location);
    formData.append('start_time', this.reservation.start_time);
    formData.append('end_time', this.reservation.end_time);
    formData.append('complete', this.reservation.complete ? '1' : '0');
    if (this.selectedFile) formData.append('image', this.selectedFile);

    const action$ = isEdit
      ? this.reservationService.edit(formData)
      : this.reservationService.add(formData);

    action$.subscribe({
      next: () => {
        this.success = isEdit
          ? 'Reservation updated successfully'
          : 'Reservation added successfully';
        this.getReservations();
        this.resetForm(form);
      },
      error: () => {
        this.error = isEdit
          ? 'Error updating reservation'
          : 'Error creating reservation';
      },
    });
  }

  editReservation(item: BookingItem): void {
    this.reservation = { ...item };
    this.isEditing = true;
    this.resetAlerts();
  }

  deleteReservation(ID: number): void {
    if (!confirm('Are you sure you want to delete this reservation?')) return;

    this.resetAlerts();

    this.reservationService.delete(ID).subscribe({
      next: () => {
        this.success = 'Reservation deleted successfully';
        this.getReservations();
        this.cdr.detectChanges();
      },
      error: () => {
        this.error = 'Failed to delete reservation';
      },
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files?.length) {
      const file = input.files[0];
      this.selectedFile = file;

      this.reservationService.uploadImage(file).subscribe({
        next: (response) => {
          this.reservation.imageName = response.fileName;
          this.success = 'Image uploaded successfully';
          this.cdr.detectChanges();
        },
        error: () => {
          this.error = 'Image upload failed';
        },
      });
    }
  }

  loadBookings(): void {
    this.http
      .get<BookingItem[]>('http://localhost/angularapp2/bookingapi/list.php')
      .subscribe({
        next: (data) => {
          this.bookings = data;
          this.cdr.detectChanges();
        },
        error: () => {
          this.error = 'Failed to load external bookings.';
        },
      });
  }

  logout(): void {
    this.auth.logout();
  }

  goToEdit(id: number): void {
    this.router.navigate(['/edit', id]);
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
      imageName: '',
    };
  }

  formatTime(time: string): string {
    if (!time) return '';
    const [hourStr, minute] = time.split(':');
    let hour = parseInt(hourStr, 10);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    hour = hour % 12 || 12;
    return `${hour}:${minute} ${ampm}`;
  }
}
