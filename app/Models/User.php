<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasMediaUpload;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasMediaUpload;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'user_type',
        'active',
        'avatar',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'user_type' => 'array',
        ];
    }

    /**
     * Check if user has a specific permission
     * 
     * @param string $module (claims, returns, payments)
     * @param string $action (view, add, edit, delete)
     * @return bool
     */
    public function hasPermission($module, $action)
    {
        if ($this->role === 'admin') {
            return true;
        }

        if (!$this->permissions || !isset($this->permissions[$module])) {
            return false;
        }

        return in_array($action, $this->permissions[$module]);
    }

    /**
     * Check if user is a reviewer (مراجع)
     * 
     * @return bool
     */
    public function isReviewer()
    {
        if ($this->role === 'admin') {
            return false;
        }
        
        return is_array($this->user_type) && in_array('مراجع', $this->user_type);
    }

    /**
     * Check if user is financial transactions (معاملات مالية)
     * 
     * @return bool
     */
    public function isFinancial()
    {
        if ($this->role === 'admin') {
            return false;
        }
        
        return is_array($this->user_type) && in_array('معاملات مالية', $this->user_type);
    }

    /**
     * Check if user can access payments module
     * 
     * @return bool
     */
    public function canAccessPayments()
    {
        if ($this->role === 'admin') {
            return true;
        }
        
        // Financial users can ONLY access payments
        if ($this->isFinancial()) {
            return true;
        }
        
        // Reviewers cannot access payments
        if ($this->isReviewer()) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if user can access non-payments modules (claims, returns, etc.)
     * 
     * @return bool
     */
    public function canAccessNonPayments()
    {
        if ($this->role === 'admin') {
            return true;
        }
        
        // Reviewers can access everything except payments
        if ($this->isReviewer()) {
            return true;
        }
        
        // Financial users cannot access non-payments
        if ($this->isFinancial()) {
            return false;
        }
        
        return true;
    }
}
