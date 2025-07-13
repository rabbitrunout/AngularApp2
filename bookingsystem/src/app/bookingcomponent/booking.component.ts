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
  public reservations: BookingItem[] = [];
  reservation: BookingItem = {
    location: '',
    start_time: '',
    end_time: '',
    complete: false,
    imageName: ''
  };

  selectedFile: File | null = null;
  error = '';
  success = '';

  constructor(
    private reservationService: BookingService,
    private http: HttpClient,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.getReservations();
  }

  getReservations(): void {
    this.reservationService.getAll().subscribe(
      (data: BookingItem[]) => {
        this.reservations = data;
        this.success = 'Reservations loaded successfully';
        this.cdr.detectChanges();
      },
      (err) => {
        this.error = 'Error loading reservations';
        console.error(err);
      }
    );
  }

  addReservation(f: NgForm): void {
  this.resetAlerts();

  const isEdit = !!this.reservation.ID;

  const proceed = () => {
    if (isEdit) {
      // РЕДАКТИРОВАНИЕ
      console.log('Editing reservation:', this.reservation);
      this.reservationService.edit(this.reservation).subscribe(
        () => {
          this.success = 'Reservation updated successfully';
          this.getReservations();
          f.resetForm();
          this.resetForm();
        },
        (err) => {
          this.error = 'Error updating reservation';
          console.error(err);
        }
      );
    } else {
      // НОВОЕ БРОНИРОВАНИЕ
      console.log('Creating reservation:', this.reservation);
      this.createReservation(f);
    }
  };

  // 🔄 Обработка загрузки изображения (если выбрано)
  if (this.selectedFile) {
    const formData = new FormData();
    formData.append('image', this.selectedFile);

    this.http.post<any>('http://localhost/angularapp2/bookingapi/upload.php', formData).subscribe(
      (uploadResponse) => {
        this.reservation.imageName = uploadResponse?.fileName || 'placeholder.jpg';
        proceed();
      },
      (error) => {
        this.error = 'Image upload failed';
        this.reservation.imageName = 'placeholder.jpg';
        proceed(); // fallback
      }
    );
  } else {
    if (!this.reservation.imageName) {
      this.reservation.imageName = 'placeholder.jpg';
    }
    proceed();
  }
}




  private createReservation(f: NgForm): void {
    this.reservationService.add(this.reservation).subscribe(
      (res: BookingItem) => {
        this.success = 'Reservation successfully created';
        this.getReservations(); // Обновляем список
        f.resetForm();
        this.resetForm();
      },
      (err) => {
        this.error = 'Error creating reservation';
        console.error(err);
      }
    );
  }

  editReservation(item: BookingItem): void {
      this.reservation = {
            ID: item.ID,
            location: item.location,
            start_time: item.start_time,
            end_time: item.end_time,
            complete: item.complete,
            imageName: item.imageName
};
}

deleteReservation(id?: number): void {
  if (id === undefined) {
    this.error = 'ID is missing';
    return;
  }

  this.reservationService.delete(id).subscribe(
    () => {
      this.success = 'Reservation deleted';
      this.getReservations();
    },
    (err) => {
      this.error = 'Delete failed';
      console.error(err);
    }
  );
}




  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
    }
  }

  private uploadFile(file: File) {
    const formData = new FormData();
    formData.append('image', file);
    return this.http.post('http://localhost/angularapp2/bookingapi/upload.php', formData);
  }

  resetForm(): void {
    this.reservation = {
      location: '',
      start_time: '',
      end_time: '',
      complete: false,
      imageName: ''
    };
    this.selectedFile = null;
  }

  resetAlerts(): void {
    this.error = '';
    this.success = '';
  }
}