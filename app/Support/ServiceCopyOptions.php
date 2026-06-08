<?php

namespace App\Support;

/**
 * Selected sections when duplicating a provider service.
 */
final class ServiceCopyOptions
{
    /**
     * @param  list<string>  $sections  Keys from {@see ServiceCopySections}.
     */
    public function __construct(public array $sections) {}

    public function includes(string $section): bool
    {
        return in_array($section, $this->sections, true);
    }

    /**
     * @param  array<string, bool|int|string>  $checked  Checkbox state keyed by section id.
     */
    public static function fromChecked(array $checked, ?string $serviceTypeCode): self
    {
        $allowed = array_flip(ServiceCopySections::forServiceTypeCode($serviceTypeCode));
        $sections = [];

        foreach (ServiceCopySections::all() as $section) {
            if (! isset($allowed[$section])) {
                continue;
            }
            if (! empty($checked[$section])) {
                $sections[] = $section;
            }
        }

        return new self($sections);
    }

    /**
     * @return list<string>
     */
    public static function defaultSectionsForServiceTypeCode(?string $code): array
    {
        return ServiceCopySections::forServiceTypeCode($code);
    }
}
