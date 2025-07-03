import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, NgForm } from '@angular/forms';
import { BookingItem } from '../bookingItem';
import { BookingService } from '../booking.service';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { ChangeDetectorRef } from '@angular/core';

@Component({
  standalone: true,
  selector: 'app-booking.component',
  imports: [HttpClientModule, CommonModule, FormsModule],
  providers: [BookingService],
  templateUrl: './booking.component.html',
  styleUrls: ['./booking.component.css'],
})
export class BookingComponent implements OnInit {
  title = 'ReservationManager';
  public reservations: BookingItem[] = [];
  reservation: BookingItem = { location: '', startTime: '', endTime: '', complete: false, imageName: '' };

  error = '';
  success = '';

  constructor(private reservationService: BookingService, private http: HttpClient, private cdr: ChangeDetectorRef) {}

  ngOnInit(): void {
    this.getReservations();
  }

  getReservations(): void {
    this.reservationService.getAll().subscribe(
      (data: BookingItem[]) => {
        this.reservations = data;
        this.success = 'successful list retrieval';
        console.log('successful list retrieval');
        console.log(this.reservations);
        this.cdr.detectChanges();
      },
      (err) => {
        console.log(err);
        this.error = 'error retrieving reservations';
      }
    );
  }

  addReservation(f: NgForm) {
    if (!this.reservation.location || !this.reservation.startTime || !this.reservation.endTime) {
      this.error = 'Please fill in all required fields.';
      return;
    }

    this.reservationService.add(this.reservation).subscribe(
      (res) => {
        this.success = 'Reservation added successfully';
        this.getReservations();
        f.resetForm();
      },
      (err) => {
        console.log(err);
        this.error = 'Failed to add reservation';
      }
    );
  }

  resetAlerts(): void {
    this.error = '';
    this.success = '';
  }
}
 