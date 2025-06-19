## Blog Api project

package used

    spatie/laravel-medialibrary
    spatie/laravel-translatable
    tymon/jwt-auth

implementation steps 

1) I made a middleware called SetLanguageMiddleware to change the language based on the Accept-Language comes from the header
2) I made a trait called HttpResponse So I can unify the response form
3) in comment migration file I put a column called parent_id to allow to make a reply on the comment
4) In PostController I made gates to prevent that no one can delete the post except the author
