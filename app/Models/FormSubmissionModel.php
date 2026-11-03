<?php

namespace App\Models;

use CodeIgniter\Model;

class FormSubmissionModel extends Model
{
    protected $table      = 'form_submissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['page_id', 'page_slug', 'data', 'ip_address', 'user_agent', 'read'];
    protected $useTimestamps = true;
    // protected $createdField = 'created_at';
}