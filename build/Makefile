# TODO: Support Windows? x86? Other platforms and architectures?

BUILD_CROSS = cross build --release
BUILD_CARGO = cargo build --release

TARGET_LINUX_X86_64 = --target=x86_64-unknown-linux-gnu
TARGET_LINUX_ARM64 = --target=aarch64-unknown-linux-gnu
TARGET_DARWIN = --target=aarch64-apple-darwin

# default is just to use cross-rs to cross-compile for all targets
# https://github.com/cross-rs/cross
BUILD_LINUX_X86_64_TARGET = $(BUILD_CROSS) $(TARGET_LINUX_X86_64)
BUILD_LINUX_ARM64_TARGET = $(BUILD_CROSS) $(TARGET_LINUX_ARM64)
BUILD_DARWIN_TARGET = $(BUILD_CROSS) $(TARGET_DARWIN)

# then override to use cargo for the native platform
UNAME_S := $(shell uname -s)
ifeq ($(UNAME_S),Linux)
	UNAME_P := $(shell uname -p)
    ifeq ($(UNAME_P),x86_64)
    	BUILD_LINUX_X86_64_TARGET = $(BUILD_CARGO) $(TARGET_LINUX_X86_64)
    	BUILD_DEFAULT_TARGET = $(BUILD_LINUX_X86_64_TARGET)
    endif
	ifneq ($(filter aarch64%,$(UNAME_P)),)
		BUILD_LINUX_ARM64_TARGET = $(BUILD_CARGO) $(TARGET_LINUX_ARM64)
		BUILD_DEFAULT_TARGET = $(BUILD_LINUX_ARM64_TARGET)
	endif
endif
ifeq ($(UNAME_S),Darwin)
	BUILD_DARWIN_TARGET = $(BUILD_CARGO) $(TARGET_DARWIN)
	BUILD_DEFAULT_TARGET = $(BUILD_DARWIN_TARGET)
endif

.PHONY: build
build: build-crc64nvme build-crc32isohdlc

.PHONY: build-all
build-all: build-crc64nvme-all build-crc32isohdlc-all

.PHONY: build-crc64nvme
build-crc64nvme: checkout-crc64nvme
	@cd crc64fast-nvme && $(BUILD_DEFAULT_TARGET)

.PHONY: build-crc32isohdlc
build-crc32isohdlc: checkout-crc32isohdlc
	@cd crc32fast-lib-rust && $(BUILD_DEFAULT_TARGET)

.PHONY: build-crc64nvme-all
build-crc64nvme-all: checkout-crc32isohdlc
	@cd crc64fast-nvme && $(BUILD_LINUX_X86_64_TARGET)
	@cd crc64fast-nvme && $(BUILD_LINUX_ARM64_TARGET)
	@cd crc64fast-nvme && $(BUILD_DARWIN_TARGET)

.PHONY: build-crc32isohdlc-all
build-crc32isohdlc-all: checkout-crc32isohdlc
	@cd crc32fast-lib-rust && $(BUILD_LINUX_X86_64_TARGET)
	@cd crc32fast-lib-rust && $(BUILD_LINUX_ARM64_TARGET)
	@cd crc32fast-lib-rust && $(BUILD_DARWIN_TARGET)

.PHONY: checkout-crc64nvme
checkout-crc64nvme:
	@if [ ! -d "./crc64fast-nvme" ]; then git clone https://github.com/awesomized/crc64fast-nvme.git; fi || true
	@cd crc64fast-nvme && git fetch && git checkout 1.1.0

.PHONY: checkout-crc32isohdlc
checkout-crc32isohdlc:
	@if [ ! -d "./crc32fast-lib-rust" ]; then git clone https://github.com/awesomized/crc32fast-lib-rust.git; fi || true
	@cd crc32fast-lib-rust && git fetch && git checkout 1.0.0

.PHONY: clean
clean:
	rm -rf crc32fast-lib-rust crc64fast-nvme
