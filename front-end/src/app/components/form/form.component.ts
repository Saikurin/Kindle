import {Component, OnInit} from '@angular/core';
import {debounceTime, distinctUntilChanged, map, Observable, OperatorFunction} from "rxjs";
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";
import {NgbTypeaheadSelectItemEvent} from "@ng-bootstrap/ng-bootstrap";
import {Book} from "../../type/book";

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})
export class FormComponent implements OnInit {

  words: string[] = [];

  books: Book[] = [];

  public model: any;

  formatter = (result: string) => result.toUpperCase();


  constructor(private httpService: HttpClient) {
  }

  ngOnInit(): void {
    this.httpService.get(environment.api_url + '/words').subscribe((data: any) => {
      this.words = data;
    });
  }

  search: OperatorFunction<string, readonly string[]> = (text$: Observable<string>) =>
    text$.pipe(
      debounceTime(200),
      distinctUntilChanged(),
      map((term) =>
        term === '' ? [] : this.words.filter((v) => v.toLowerCase().indexOf(term.toLowerCase()) > -1).slice(0, 10),
      ),
    );

  selectBook($event: NgbTypeaheadSelectItemEvent<any>) {
    this.books = [];
    this.httpService.get(environment.api_url + "/word/" + $event.item).subscribe((data: any) => {
      this.books = data;
    });
  }
}
