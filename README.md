Blog API Project

Packages Used:
        spatie/laravel-medialibrary

        spatie/laravel-translatable

        tymon/jwt-auth

design pattern used
        Service pattern

Implementation Steps:
        I created a middleware called SetLanguageMiddleware to change the language based on the Accept-Language header.

        I developed a trait called HttpResponse to standardize the response format across the API.

        In the comments migration file, I added a column named parent_id to enable nested comments/replies.

        In the PostController, I implemented Gates to ensure only the post author can delete a post.
