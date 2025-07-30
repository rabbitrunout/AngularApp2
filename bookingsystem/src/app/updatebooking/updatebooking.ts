import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { HttpClient, HttpClientModule, HttpErrorResponse } from '@angular/common/http';
import { NgForm, FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { BookingItem } from '../bookingItem';
import { Auth } from '../services/auth';

@Component({
  standalone: true,
  selector: 'app-updatebooking',
  imports: [CommonModule, HttpClientModule, FormsModule],
  templateUrl: './updatebooking.html',
  styleUrls: ['./updatebooking.css'],
  providers: [Auth]
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

  success: string = '';
  error: string = '';
  selectedFile: File | null = null;
  previewUrl: string | null = null;
  originalImageName: string = '';

  constructor(
    private route: ActivatedRoute,
    private http: HttpClient,
    public authService: Auth,
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

    this.loadBookingData();
  }

  loadBookingData(): void {
  this.http.get<{data: BookingItem}>(`http://localhost/angularapp2/bookingapi/view.php?id=${this.bookingID}`)
    .subscribe({
      next: (response) => {
        console.log('Response from server:', response);
        const data = response.data;
        this.reservation = {
          ID: data.ID,
          location: data.location || '',
          start_time: this.formatDateTimeLocal(data.start_time),
          end_time: this.formatDateTimeLocal(data.end_time),
          complete: Boolean(data.complete),
          imageName: data.imageName || ''
        };
        this.originalImageName = this.reservation.imageName || '';
        this.previewUrl = this.originalImageName
          ? `http://localhost/angularapp2/bookingapi/uploads/${this.originalImageName}`
          : null;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error loading booking data', err);
        this.error = 'Error when uploading booking data.';
      }
    });
}


  formatDateTimeLocal(dateStr: string): string {
    if (!dateStr) return '';
    const dt = new Date(dateStr.replace(' ', 'T'));
    return dt.toISOString().slice(0, 16);
  }

  toSqlDateTimeLocal(dateStr: string): string {
    if (!dateStr) return '';
    return dateStr.replace('T', ' ') + ':00';
  }

  onFileSelected(event: any): void {
    const file = event.target.files[0];
    if (file) {
      this.selectedFile = file;
      this.reservation.imageName = file.name;

      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.previewUrl = e.target.result;
        this.cdr.detectChanges();
      };
      reader.readAsDataURL(file);
    }
  }

  updateReservation(form: NgForm): void {
  if (form.invalid) return;

  const formData = new FormData();
  formData.append('ID', this.bookingID.toString());
  formData.append('location', this.reservation.location || '');
  formData.append('start_time', this.toSqlDateTimeLocal(this.reservation.start_time));
  formData.append('end_time', this.toSqlDateTimeLocal(this.reservation.end_time));
  formData.append('complete', this.reservation.complete ? '1' : '0');
  formData.append('existingImage', this.originalImageName || '');

  if (this.selectedFile) {
    formData.append('image', this.selectedFile);
  }

  this.http.post('http://localhost/angularapp2/bookingapi/edit.php', formData, {
  responseType: 'text'
}).subscribe({
  next: (responseText) => {
    try {
      const response = JSON.parse(responseText);
      console.log('Parsed JSON:', response);

      if (response && response.success) {
        this.success = response.message || 'Booking updated successfully';
        this.error = '';
        setTimeout(() => this.router.navigate(['/booking']), 1000);
      } else {
        this.error = response?.error || 'Update error';
        this.success = '';
      }
    } catch (e) {
      console.error('JSON parse error:', e, responseText);
      this.error = 'Update error (incorrect JSON)';
      this.success = '';
    }
  },
  error: (err: HttpErrorResponse) => {
    console.error('POST error:', err);
    this.error = err.error?.error || 'Update error';
    this.success = '';
    this.cdr.detectChanges();
  }
});
}

  cancelUpdate(): void {
    this.router.navigate(['/booking']);
  }
}
