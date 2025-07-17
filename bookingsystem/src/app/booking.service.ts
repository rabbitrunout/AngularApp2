import { Injectable } from "@angular/core";
import { HttpClient, HttpParams } from "@angular/common/http";
import { map, Observable } from "rxjs";
import { BookingItem } from "./bookingItem";

@Injectable({ providedIn: 'root' })
export class BookingService {

  baseUrl = 'http://localhost/angularapp2/bookingapi';

  constructor(private http: HttpClient) {}

  getAll(): Observable<BookingItem[]> {
  return this.http.get<{ data: BookingItem[] }>(`${this.baseUrl}/list.php`).pipe(
    map(res => res.data)
  );
}


 getById(id: number): Observable<BookingItem> {
  const params = new HttpParams().set('id', id.toString());  // 'id', не 'ID'
  return this.http.get<BookingItem>(`${this.baseUrl}/view.php`, { params });
}



  add(formData: FormData): Observable<BookingItem> {
    return this.http.post<BookingItem>(`${this.baseUrl}/add.php`, formData);
  }

  edit(formData: FormData): Observable<BookingItem> {
  return this.http.post<BookingItem>(`${this.baseUrl}/edit.php`, formData);
}


  delete(ID: number): Observable<any> {
    const params = new HttpParams().set('ID', ID.toString());
    return this.http.delete(`${this.baseUrl}/delete.php`, { params });
  }

  uploadFile(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('image', file);
    return this.http.post(`${this.baseUrl}/upload.php`, formData);
  }
}
