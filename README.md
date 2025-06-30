# Filer

> The filer bundle is a symfony bundle that allow you to manage files storage.

#### Usage

1- Configuration

```
# /config/packages/filer.yaml
filer:
    types:
        profile_picture:
            folder: profile_picture
            access: public
            mime_types: [image/jpeg, image/png]
            max_size: 5000000
            filters: [thumbnail]
            apply_watermarK: true
            keep_source: false
            source: thumbnail
    accesses:
        private: 'private_filesystem'
        public: 'public_filesystem'
```

> `private_filesystem` and `public_filesystem` should be services alias of `Gaufrette\FilesystemInterface`.
> `thumbnail` must be a defined filter in `liip_imagine` `filter_sets` configuration.

2- Example

```
<?php

namespace App;

classe MyService {
    
    public function __construct(private FilerInterface $passwordFiler, private FilerInterface $emailFiler)
    {
    }
    
    public function testEncryptPassword(string $text): string
    {
        return $this->passwordFiler->encrypt($text);
    }
    
    public function testDecryptPassword(string $text): string
    {
        return $this->passwordFiler->decrypt($text);
    }
}
```
