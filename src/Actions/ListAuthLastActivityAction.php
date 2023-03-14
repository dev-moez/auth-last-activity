<?php
namespace DevMoez\AuthLastActivity\Actions;

use DevMoez\AuthLastActivity\Models\AuthLastActivity;
use Illuminate\Contracts\Pagination\Paginator;

class ListAuthLastActivityAction {

    public function execute(): Paginator
    {
        return AuthLastActivity::query()->paginate();
    }
}