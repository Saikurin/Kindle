import {Component, OnInit} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";
import {Book} from "../../type/book";

@Component({
  selector: 'app-form-regexp',
  templateUrl: './form-regexp.component.html',
  styleUrls: ['./form-regexp.component.scss']
})
export class FormRegexpComponent implements OnInit {

  words: string[] = [];

  books: Book[] = [];

  constructor(private httpService: HttpClient) {
  }

  ngOnInit(): void {
  }

  search = ({$event}: { $event: any })  => {
    // Send POST request x-www-form-urlencoded to /search-regexp
    // with body {regexp: $event}
    // and return the result
    this.httpService.get(environment.api_url + '/search-regexp/' + encodeURIComponent($event.target.value)).subscribe((data: any) => {
      console.log(data);
      this.books = data;
    });
  }
}
