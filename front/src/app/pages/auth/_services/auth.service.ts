import { Injectable } from '@angular/core';
import { HttpHeaders, HttpClient, HttpErrorResponse } from '@angular/common/http';
import { User } from '../user';
import { Router } from '@angular/router';
import { Observable, throwError } from 'rxjs';
import { map, catchError, tap } from 'rxjs/operators';
import { environment } from 'src/environments/environment';
import { LoginRequest } from './requestResponsse/loginRequest';
import { RegisterRequest } from './requestResponsse/registerRequest';
import { RegisterResponse } from './requestResponsse/registerResponse';

const httpOptions = {
  headers: new HttpHeaders({
    'Content-Type': 'application/json'
  })
};

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  public currentUser: User;
  private readonly apiUrl = environment.apiUrl;
  private registerUrl = this.apiUrl + '/auth/signup';
  private loginUrl = this.apiUrl + '/auth/login';

  constructor(
    private http: HttpClient,
    private router: Router
  ) { }

  onRegister(registerRequest: RegisterRequest): Observable<RegisterResponse> {
    const request = JSON.stringify({
      name: registerRequest.name,
      email: registerRequest.email,
      password: registerRequest.password,
      password_confirmation: registerRequest.passwordConfirmaion
    });

    return this.http.post(this.registerUrl, request, httpOptions)
      .pipe(
        map((response: RegisterResponse) => {
          const message: string = response.message;
          // if (token) {
          //   this.setToken(token);
          //   this.getUser().subscribe();
          // }
          return response;
        }),
        catchError(error => this.handleError(error))
      );
  }

  // onLogin(loginRequest: LoginRequest): Observable<User> {
  //   const request = JSON.stringify({
  //     email: loginRequest.email,
  //     password: loginRequest.password,
  //     remember_me: loginRequest.rememberMe
  //   });
  //   return this.http.post(this.registerUrl, request, httpOptions)
  //     .pipe(
  //       map((response: User) => {
  //         const token: string = response['access_token'];
  //         if (token) {
  //           this.setToken(token);
  //           this.getUser().subscribe();
  //         }
  //         return response;
  //       }),
  //       catchError(error => this.handleError(error))
  //     );
  // }


  // onLogout(): Observable<User> {
  //   return this.http.post(this.apiUrl + '/logout', httpOptions).pipe(
  //     tap(
  //       () => {
  //         localStorage.removeItem('token');
  //         this.router.navigate(['/']);
  //       }
  //     )
  //   );
  // }

  setToken(token: string): void {
    return localStorage.setItem('token', token);
  }
  getToken(): string {
    return localStorage.getItem('token');
  }

  getUser(): Observable<User> {
    return this.http.get(this.apiUrl + '/me')
      .pipe(
        tap(
          (user: User) => {
            this.currentUser = user;
          }
        )
      );
  }

  isAuthenticated(): boolean {
    const token: string = this.getToken();
    if (token) {
      return true;
    }
    return false;
  }

  private handleError(error: HttpErrorResponse) {
    if (error.error instanceof ErrorEvent) {
      console.error('Un error a ocurrido: ', error.error.message);
    } else {
      return throwError(error);
    }
    return throwError('Oh, algo malo sucede aquí; Por favor, inténtelo de nuevo más tarde.');
  }
}
