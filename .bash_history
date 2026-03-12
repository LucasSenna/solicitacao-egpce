php artisan migrate
php artisan make:filament-page NewRequest
exit
php artisan make:livewire Requests/Index
exit
php artisan optimize:clear
composer dump-autoload
php artisan optimize:clear
composer show livewire/livewire
exit
php artisan optimize:clear
php artisan optimize:clear
php artisan optimize:clear
php artisan optimize:clear
php artisan optimize:clear
php artisan cache:clear
exit
php artisan migrate
exit
php artisan optimize:clear
php artisan storage:link
php artisan migrate
exit
npm install sweetalert2
docker compose exec node sh -lc "npm i sweetalert2"
exit
php artisan make:model Space -m
php artisan make:model Equipment -m
php artisan make:model SpaceRequest -m
php artisan make:seeder SpaceEquipmentSeeder
php artisan make:notification NewSpaceRequestNotification
php artisan make:livewire SpaceRequest/Create
exit
php artisan migrate
php artisan db:seed --class=SpaceEquipmentSeeder
php artisan db:seed --class=SpaceEquipmentSeeder
php artisan db:seed --class=SpaceEquipmentSeeder
exit
php artisan optimize:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan event:clear
exit
php artisan make:migration add_event_type_and_participants_to_training_requests_table --table=training_requests
cd /home/lucas/dev
sudo chown -R lucas:lucas solicitacao
exit
