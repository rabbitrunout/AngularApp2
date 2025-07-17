import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { HttpClient, HttpClientModule, HttpErrorResponse } from '@angular/common/http';
import { NgForm, FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { BookingItem } from '../bookingItem';

@Component({
  selector: 'app-updatebooking',
  standalone: true,
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './updatebooking.html',
  styleUrls: ['./updatebooking.css']
})
export class UpdatebookingComponent implements OnInit {
  bookingID!: number;
  reservation: BookingItem = {
    ID: 0,
    location: '',
    start_time: '',
    end_time: '',
    complete: false,
    imageName: ''
  };

  success = '';
  error = '';
  selectedFile: File | null = null;
  previewUrl: string | null = null;
  originalImageName: string = '';

  constructor(
    private route: ActivatedRoute,
    private http: HttpClient,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
  const idParam = this.route.snapshot.paramMap.get('id');
  if (!idParam) {
    this.error = 'Invalid booking ID';
    return;
  }

  this.bookingID = +idParam;
  if (this.bookingID <= 0) {
    this.error = 'Invalid booking ID';
    return;
  }

  this.http.get<BookingItem>(`http://localhost/angularapp2/bookingapi/view.php?id=${this.bookingID}`)
    .subscribe({
      next: (data) => {
        this.reservation = {
          ...data,
          complete: Boolean(data.complete),
          imageName: (data as any).image_name || data.imageName || ''
        };
        this.originalImageName = this.reservation.imageName || '';
        this.previewUrl = `http://localhost/angularapp2/bookingapi/uploads/${this.originalImageName}`;
        this.cdr.detectChanges();
      },
      error: () => this.error = 'Ошибка при загрузке данных бронирования.'
    });
}

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
      this.reservation.imageName = this.selectedFile.name;

      const reader = new FileReader();
      reader.onload = () => {
        this.previewUrl = reader.result as string;
        this.cdr.detectChanges();
      };
      reader.readAsDataURL(this.selectedFile);
    }
  }

  updateReservation(form: NgForm): void {
    if (form.invalid) return;

    const formData = new FormData();
    formData.append('ID', this.bookingID.toString());
    formData.append('location', this.reservation.location || '');
    formData.append('start_time', this.reservation.start_time || '');
    formData.append('end_time', this.reservation.end_time || '');
    formData.append('complete', this.reservation.complete ? '1' : '0');
    formData.append('existingImage', this.originalImageName || '');

    if (this.selectedFile) {
      formData.append('image', this.selectedFile);
    }

    this.http.post('http://localhost/angularapp2/bookingapi/edit.php', formData).subscribe({
      next: () => {
        this.success = 'Бронирование обновлено успешно';
        this.router.navigate(['/booking']);

      },
      error: (err: HttpErrorResponse) => {
        this.error = err.error?.error || 'Ошибка обновления';
        this.cdr.detectChanges();
      }
    });
  }

  cancelUpdate(): void {
    this.router.navigate(['/booking']);
  }
}
