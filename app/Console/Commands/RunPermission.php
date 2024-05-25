<?php

namespace App\Console\Commands;

use App\Models\Permission\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RunPermission extends Command
{
    public function __construct(
        private Permission $permission,
    ) {
        parent::__construct();
        $this->permission = $permission;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:permission';
    protected $path = 'permission.php';
    protected $controllerPath = "App\Http\Controllers\Api\\";


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for updating or creating permissions in {permission.php} and in {database}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("Process started! " . now());
        $this->executePermission();
    }

    public function executePermission()
    {
        $this->putPermissionIntoFile();
        $this->updatePermissionInDB();
        $this->deleteRemovedPermisionsFromDB();
        $this->info("Permission updated and saved successfully! " . now());
    }

    public function updatePermissionInDB()
    {
        $file_content = $this->getFileContent();
        foreach ($file_content as $key => $value) {
            $this->permission->updateOrCreate(Arr::except($value, ['description_for_controller', 'description_for_action']), $value);
        }
    }
    public function deleteRemovedPermisionsFromDB()
    {
        $file_content = $this->getFileContent();
        $mappedFileContent = collect($file_content)->map(fn ($item) => $item['controller'] . $item['action'])->all();
        $this->permission->whereNotIn(DB::raw("CONCAT(controller,action)"), $mappedFileContent)->delete();
    }

    public function putPermissionIntoFile()
    {
        $route_permissions = $this->getPermissionsFromRoute();
        $data = [];
        foreach ($route_permissions as $key => $route) {
            $data[] = $this->getValueFromFile($route['controller'], $route['action']) ?? $route;
        }
        // $data = array_merge($data, $this->additionalPermissions());
        $content = "<?php\n\n return " . var_export($data, true) . "; \n\n?>";
        File::put($this->path, '');
        File::put($this->path, $content);
    }

    public function getValueFromFile(string $controller = '', string $action = '')
    {
        $result = null;
        if ($controller && $action) {
            $file_content = $this->getFileContent();
            $result = collect($file_content)->where('controller', $controller)->where('action', $action)->first();
        }
        return $result;
    }

    public function getPermissionsFromRoute()
    {
        $routes = Route::getRoutes();

        $data = [];
        foreach ($routes as $route) {
            $action = $route->getAction();

            if (isset($action['controller'])) {
                if (str_starts_with($action['controller'], $this->controllerPath)) {
                    $controllerWithAction = trim(str_replace($this->controllerPath, '', $action['controller']));
                    $explode = explode('@', $controllerWithAction);
                    $data[] = [
                        'controller' => $explode[0],
                        'action' => $explode[1],
                    ];
                }
            }
        }
        return $data;
    }

    public function getFileContent()
    {
        return require($this->path);
    }
}
