import { Component, OnInit } from '@angular/core';
import { FormsModule, NgForm } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { BookingItem } from '../bookingItem';
import { BookingService } from '../booking.service';
import { CommonModule } from '@angular/common';
import { ChangeDetectorRef } from '@angular/core';

@Component({
  standalone: true,
  selector: 'app-booking',
  templateUrl: './booking.component.html',
  styleUrls: ['./booking.component.css'],
  imports: [CommonModule, FormsModule],
  providers: [BookingService],
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

  constructor(private reservationService: BookingService, 
              private http: HttpClient,
              private cdr: ChangeDetectorRef) 
              {}

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

  // getReservations(): void {
  //   this.reservationService.getAll().subscribe(
  //     (data) => (this.reservations = data),
  //     (err) => (this.error = 'Failed to load reservations')
  //   );
  // }

  addReservation(f: NgForm): void {
    this.resetAlerts();
    const isEdit = !!this.reservation.ID;

    const proceed = () => {
      if (isEdit) {
        this.reservationService.edit(this.reservation).subscribe(
          () => {
            this.success = 'Reservation updated';
            this.getReservations();
            this.resetForm(f);
          },
          (err) => (this.error = 'Error updating reservation')
        );
      } else {
        this.reservationService.add(this.reservation).subscribe(
          (createdRaservation) => {
            this.success = 'Reservation added';
            this.reservations = [
              this.reservation,
              createdRaservation
            ];
            this.getReservations();
            this.resetForm(f);
          },
          (err) => (this.error = 'Error creating reservation')
        );
      }
    };

    if (this.selectedFile) {
      const formData = new FormData();
      formData.append('image', this.selectedFile);
      this.http.post<any>('http://localhost/angularapp2/bookingapi/upload.php', formData).subscribe(
        (res) => {
          this.reservation.imageName = res?.fileName || '';
          proceed();
        },
        () => {
          this.error = 'Image upload failed';
          this.reservation.imageName = '';
          proceed();
        }
      );
    } else {
      if (!this.reservation.imageName) {
        this.reservation.imageName = '';
      }
      proceed();
    }
  }

  editReservation(res: BookingItem): void {
  this.reservation = {
    ...res,
    complete: res.complete ?? false,
    imageName: res.imageName ?? ' '
  };
  this.isEditing = true;
}

  deleteReservation(ID?: number): void {
    if (!ID) return;
    this.reservationService.delete(ID).subscribe(
      () => {
        this.success = 'Deleted successfully';
        this.getReservations();
      },
      () => (this.error = 'Delete failed')
    );
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
      this.selectedFile = input.files[0];
    }
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
    f?.resetForm();
  }

  resetAlerts(): void {
    this.error = '';
    this.success = '';
  }
}
