<?php

declare(strict_types=1);

namespace Sokil\KeycloakAdminApiClient\ValueObject;

enum UserAfterRegistrationAction: string
{
    case ConfigureTotp = "CONFIGURE_TOTP";
    case UpdatePassword = "UPDATE_PASSWORD";
    case UpdateProfile = "UPDATE_PROFILE";
    case VerifyEmail = "VERIFY_EMAIL";
    case UpdateUserLocale = "update_user_locale";
}
