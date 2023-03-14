<?php
namespace DevMoez\AuthLastActivity\Actions;

use DevMoez\AuthLastActivity\Models\AuthLastActivity;

class DeleteAuthLastActivityAction {

    public function __construct(public readonly int $id)
    {}

    public function execute(): bool
    {
        return AuthLastActivity::find($this->id)->delete();
    }
}