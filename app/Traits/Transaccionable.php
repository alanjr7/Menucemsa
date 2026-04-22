<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Closure;

trait Transaccionable
{
    protected function transaction(Closure $callback, ?Closure $onError = null): mixed
    {
        DB::beginTransaction();

        try {
            $result = $callback();
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($onError) {
                return $onError($e);
            }

            throw $e;
        }
    }
}
