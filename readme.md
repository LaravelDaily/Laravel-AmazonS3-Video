# Laravel 5.5 demo: uploading videos to Amazon S3

![Larancer screenshot](http://webcoderpro.com/s3-videos-demo.png)

## How to use

- Clone the repository with __git clone__
- Copy __.env.example__ file to __.env__ and edit database credentials there
- Don't forget to enter your Amazon credentials in __.env__ file - as AWS_KEY / AWS_SECRET / AWS_REGION / AWS_BUCKET
- Run __composer install__
- Run __php artisan key:generate__
- Run __php artisan migrate --seed__ (it has some seeded data for your testing)
- That's it: launch the main URL and login with default credentials __admin@admin.com__ - __password__

## Credits

- [Spatie Laravel MediaLibrary](https://github.com/spatie/laravel-medialibrary)
- [BlueIMP jQuery File Upload](https://blueimp.github.io/jQuery-File-Upload/)

## License

Basically, feel free to use and re-use any way you want.