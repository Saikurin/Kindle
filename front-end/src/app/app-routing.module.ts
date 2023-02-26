import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {HomeComponent} from "./screens/home/home.component";
import {BooksAvailablesComponent} from "./screens/books-availables/books-availables.component";
import {ReaderComponent} from "./screens/reader/reader.component";

const routes: Routes = [
  {path: 'home', component: HomeComponent},
  {path: 'books', component: BooksAvailablesComponent},
  {path: 'reader/:id', component: ReaderComponent},
  {path: '', component: HomeComponent, pathMatch: 'full'},
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
