import {Component, OnInit} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";
import {Book} from "../../type/book";

@Component({
  selector: 'app-books-availables',
  templateUrl: './books-availables.component.html',
  styleUrls: ['./books-availables.component.scss']
})
export class BooksAvailablesComponent implements OnInit {

  books: Book[] = [];

  page = 1;
  pageSize = 8;

  constructor(private httpService: HttpClient) {
  }

  ngOnInit(): void {
    this.httpService.get(environment.api_url + '/books').subscribe((data: any) => {
      this.books = data;
    });
  }

}
