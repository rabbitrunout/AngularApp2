import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { HttpClient, HttpClientModule, HttpErrorResponse } from '@angular/common/http';
import { NgForm, FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { BookingItem } from '../bookingItem';

@Component({
  selector: 'app-updatebooking',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule, RouterModule],
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

  selectedFile: File | null = null;
  previewUrl: string | null = null;
  originalImageName: string = '';

  success = '';
  error = '';

  constructor(
    private route: ActivatedRoute,
    private http: HttpClient,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.bookingID = +this.route.snapshot.paramMap.get('id')!;
    this.http.get<BookingItem>(`http://localhost/your-api/getbooking.php?ID=${this.bookingID}`)
      .subscribe({
        next: (data: BookingItem) => {
          this.reservation = data;
          this.originalImageName = data.imageName || '';
          this.previewUrl = `http://localhost/your-api/uploads/${this.originalImageName}`;
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
    formData.append('location', this.reservation.location);
    formData.append('start_time', this.reservation.start_time);
    formData.append('end_time', this.reservation.end_time);
    formData.append('complete', this.reservation.complete ? '1' : '0');
    formData.append('existingImage', this.originalImageName);

    if (this.selectedFile) {
      formData.append('image', this.selectedFile);
    }

    this.http.post('http://localhost/your-api/edit.php', formData).subscribe({
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
