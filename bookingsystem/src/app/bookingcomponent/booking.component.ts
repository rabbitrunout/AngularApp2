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

  selectedFile: File | null = null;
  error = '';
  success = '';
  bookingcomponent: any;

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
    const formData = new FormData();
    formData.append('image', this.selectedFile);

   this.http.post<any>('http://localhost/angularapp2-1/bookingapi/upload', formData).subscribe(
      (uploadResponse) => {
        this.reservation.imageName = this.selectedFile?.name || '';

        // После загрузки — отправляем саму бронь
        this.createReservation(f);
      },
      (error) => {
        this.error = 'Image upload failed';
      }
    );
  } else {
    this.createReservation(f);
  }
}

createReservation(f: NgForm): void {
  this.reservationService.add(this.reservation).subscribe(
    (res: BookingItem) => {
      this.success = 'Successfully created';
      this.getReservations();
      f.resetForm();

      // 🧹 Сброс данных формы
      this.reservation = { location: '', startTime: '', endTime: '', complete: false, imageName: '' };
      this.selectedFile = null;
    },
    (err) => {
      this.error = err.message || 'Error creating reservation';
    }
  );
}



  uploadFile(): void {
  if (!this.selectedFile) {
    return;
  }

  const formData = new FormData();
  formData.append('image', this.selectedFile);

  this.http.post<any>('http://localhost/angularapp2-1/bookingapi/upload', formData).subscribe(
    (response) => {
      console.log('File upload successful:', response);
      // Добавим имя файла в объект reservation:
      this.reservation.imageName = this.selectedFile?.name || '';
    },
    (error) => {
      console.error('File upload failed:', error);
    }
  );
}


  onFileSelected(event: Event): void
  {
    const input = event.target as HTMLInputElement;
    if(input.files && input.files.length > 0)
    {
      this.selectedFile = input.files[0];
    }
  }

  resetAlerts(): void {
    this.error = '';
    this.success = '';
  }
}
 