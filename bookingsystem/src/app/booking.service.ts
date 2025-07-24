import { Injectable } from "@angular/core";
import { HttpClient, HttpHeaders, HttpParams } from "@angular/common/http";
import { map, Observable } from "rxjs";
import { BookingItem } from "./bookingItem";

@Injectable({ providedIn: 'root' })
export class BookingService {

  baseUrl = 'http://localhost/angularapp2/bookingapi';

  constructor(private http: HttpClient) {}

 getAll(): Observable<BookingItem[]> {
  const userName = localStorage.getItem('userName') || '';
  const params = new HttpParams().set('userName', userName);

  return this.http
    .get<{ data: BookingItem[] }>(`${this.baseUrl}/list.php`, { params })
    .pipe(map(res => res.data));
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
  return this.http.post(`${this.baseUrl}/delete.php`, { ID });
}



  uploadImage(file: File): Observable<{ fileName: string }> {
  const formData = new FormData();
  formData.append('image', file);

  return this.http.post<{ fileName: string }>(
    'http://localhost/angularapp2/bookingapi/upload.php',
    formData
  );
}


  uploadFile(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('image', file);
    return this.http.post(`${this.baseUrl}/upload.php`, formData);
  }
}
