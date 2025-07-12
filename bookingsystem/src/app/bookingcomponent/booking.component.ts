import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, NgForm } from '@angular/forms';
import { BookingItem } from '../bookingItem';
import { BookingService } from '../booking.service';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { ChangeDetectorRef } from '@angular/core';
import { RouterModule } from '@angular/router';


@Component({
  standalone: true,
  selector: 'app-booking.component',
  imports: [HttpClientModule, CommonModule, FormsModule, RouterModule],
  providers: [BookingService],
  templateUrl: './booking.component.html',
  styleUrls: ['./booking.component.css'],
})
export class BookingComponent implements OnInit {
  title = 'ReservationManager';
  public reservations: BookingItem[] = [];
  reservation: BookingItem = { location: '', start_time: '', end_time: '', complete: false, imageName: '' };


  error = '';
  success = '';
  selectedFile: File | null = null;

  constructor(private reservationService: BookingService, private http: HttpClient, private cdr: ChangeDetectorRef) {}

  ngOnInit(): void {
    this.getReservations();
  }

  getReservations(): void {
  this.reservationService.getAll().subscribe(
    (data: BookingItem[]) => {
      // console.log('Полученные брони:', data);  // <-- добавь эту строку
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
  this.resetAlerts();

  if (this.selectedFile) {
    this.reservation.imageName = this.selectedFile.name;
    this.uploadFile(); // uploads the file
  } else {
    this.reservation.imageName = ''; // let the backend assign placeholder
  }

  this.reservationService.add(this.reservation).subscribe(
    (res: BookingItem) => {
      this.reservations.push(res);
      this.success = 'Successfully created';
      f.reset();
      this.selectedFile = null; // reset file
    },
    (err) => (this.error = err.message)
  );
}

editReservation(location: any, start_time: any, end_time: any, complete: boolean, ID: any) {
  this.resetAlerts();
  this.reservationService.edit({
    location: location.value,
    start_time: start_time.value,
    end_time: end_time.value,
    complete: complete,
    ID: +ID
  }).subscribe(
    (res) => {
      this.cdr.detectChanges();
      this.success = 'Successfully edited';
    },
    (err) => {
      this.error = err.message;
    }
  );
}

deleteReservation(ID: number) {
  console.log('Delete clicked for ID:', ID); // <-- Add this
  this.resetAlerts();
  this.reservationService.delete(ID).subscribe(
    (res) => {
      this.reservations = this.reservations.filter(function (item) {
        return item['ID'] && +item['ID'] !== +ID;
      });
      this.cdr.detectChanges();
      this.success = 'Deleted successfully';
    },
    (err) => (this.error = err.message)
  );
}

  uploadFile(): void {
  if (!this.selectedFile) {
    return;
  }
  const formData = new FormData();
  formData.append('image', this.selectedFile);

  this.http.post('http://localhost/angularapp2/bookingsapi/upload', formData).subscribe(
    response => console.log('File uploaded successfully: ', response),
    error => console.error('File upload failed: ', error)
  );
}

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
    }
  }

  resetAlerts(): void {
    this.error = '';
    this.success = '';
  }
}
 