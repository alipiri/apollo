<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Models\V1\Company;
use App\Models\V1\Contact;
use App\Models\V1\Note;

class CompanyPolicy
{

    /**
     * Determine whether the user can update the model.
     */
    public function update(Company $company, Company $model): bool
    {
        return $company->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Company $company, Company $model): bool
    {
        return $company->id === $model->id;
    }
}
