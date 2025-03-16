<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ApprovalStatus: string implements HasColor, HasIcon, HasLabel
{
  case DRAFT = 'draft';
  case PENDING = 'pending';
  case APPROVED = 'approved';
  case REJECTED = 'rejected';

  public function getLabel(): ?string
  {
    return match ($this) {
      self::DRAFT => 'Draft',
      self::PENDING => 'Pending',
      self::APPROVED => 'Approved',
      self::REJECTED => 'Rejected',
    };
  }

  public function getColor(): string|array|null
  {
    return match ($this) {
      self::DRAFT => 'primary',
      self::PENDING => 'warning',
      self::APPROVED => 'success',
      self::REJECTED => 'danger',
    };
  }

  public function getIcon(): ?string
  {
    return match ($this) {
      self::DRAFT => 'fluentui-pen-32',
      self::PENDING => 'fluentui-calendar-settings-28',
      self::APPROVED => 'fluentui-checkmark-starburst-16',
      self::REJECTED => 'fluentui-checkmark-starburst-16',
    };
  }
}
