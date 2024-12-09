# project-books-collection
REST ful API for application books collection

## description project
This simple rest ful api project CRUD for manage books collection

## feature
 role user
- Register, Login and Logout
- See all books collection
- See detail book
- Create book
- Edit book (delete his own book)
- Delete book (delete his own book)
- See detail user
- Delete user (delete his own account)
- See all categories
- See detail category
role admin
- Create user
- Edit user
- Delete user (all user)
- See all users
- See all categories
- See detail category
- Add category
- Edit category
- Delete category
- See all books collection
- See detail book
- Create book
- Edit book (all book)
- Delete book (all book)

## technology
- Laravel 11
- Mysql
- Json Web Token (JWT)

## Installation

Install project-books-collection with composer


After clone project install composer
```bash
  composer install
```
create file .env & add this code to .env for configurate

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=books_collection
DB_USERNAME=root
DB_PASSWORD=

# change file system disk

FILESYSTEM_DISK=local
# to
FILESYSTEM_DISK=public

# add jwt blacklist exception

JWT_SHOW_BLACKLIST_EXCEPTION=true
```

generate key
```bash
  php artisan key:generate
```

generate key jwt
```bash
  php artisan jwt:secret
```

migration
```bash
  php artisan migrate
```

generate data dummy
```bash
  php artisan db:seed
```

and now
```bash
  php artisan serve
```

    
## Authors

- [@muhammaddzakiardiansyah](https://www.github.com/muhammaddzakiardiansyah)
