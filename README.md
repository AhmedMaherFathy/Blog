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


Post 
        Multilingual Content Management
        The system stores both titles and content in bilingual format (English and Arabic) using JSON data type, enabling seamless multilingual support.

        Automated Slug Generation
        Slugs are automatically generated from the English version of titles

        Implements duplicate prevention by appending incremental numbers when identical slugs exist

        Ensures each post maintains a unique URL identifier

        Image Handling Architecture
        Media management methods are encapsulated within the Post model

        Service classes coordinate the image storage process following SOLID principles

        Date Presentation
        Created timestamps are formatted for improved human readability

        Delivers user-friendly date display while maintaining ISO standards
