import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import {NgbModule, NgbPaginationModule} from '@ng-bootstrap/ng-bootstrap';
import { HomeComponent } from './screens/home/home.component';
import { HeaderComponent } from './components/header/header.component';
import { FormComponent } from './components/form/form.component';
import { BooksAvailablesComponent } from './screens/books-availables/books-availables.component';
import { BookComponent } from './components/book/book.component';
import {HttpClientModule} from "@angular/common/http";
import {FormsModule} from "@angular/forms";
import { FormRegexpComponent } from './components/form-regexp/form-regexp.component';
import {AngularFireModule} from "@angular/fire/compat";
import {environment} from "../environments/environment.prod";
import { ReaderComponent } from './screens/reader/reader.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    HeaderComponent,
    FormComponent,
    BooksAvailablesComponent,
    BookComponent,
    FormRegexpComponent,
    ReaderComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    NgbModule,
    HttpClientModule,
    FormsModule,
    AngularFireModule.initializeApp(environment.firebase),
    NgbPaginationModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
