import React from 'react';
import { Fragment, useContext, useState } from 'react';
import { Dialog, Disclosure, Popover, Transition } from '@headlessui/react';
import { Bars3BottomRightIcon, XMarkIcon } from '@heroicons/react/24/outline';
import { ChevronDownIcon, LockClosedIcon } from '@heroicons/react/20/solid';
import { headerCategories, headerPanelCategories } from '@/app/_lib/HeaderData';
import DriveFileRenameOutlineIcon from '@mui/icons-material/DriveFileRenameOutline';
import AccountCircleIcon from '@mui/icons-material/AccountCircle';
import LogoutIcon from '@mui/icons-material/Logout';

import MainLogo from '../../../../public/pictures/logo/salt_and_pepper_logo.png';
import { AppContext } from '@/app/_lib/_context/AppContext';
import { useRouter } from 'next/navigation';
import { PROJECT_ROUTE } from '@/app/_lib/_router/Routes';
import { removeCurrentUserCookie } from '@/app/_lib/_cookie/CookieActions';
import { GET_USER_TYPE } from '@/app/_lib/_type/UserTypes';
import PictureService from '@/app/_service/PictureService';

function classNames(...classes: string[]) {
  return classes.filter(Boolean).join(' ');
}

export default function Header() {
  const setLinkClicked = useContext(AppContext)
    ?.setLinkClicked as React.Dispatch<React.SetStateAction<boolean>>;
  const setUser = useContext(AppContext)?.setUser as React.Dispatch<
    React.SetStateAction<GET_USER_TYPE>
  >;
  const userAuthenticated = useContext(AppContext)?.userAuthenticated;
  const user = useContext(AppContext)?.user;
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const router = useRouter();

  const handleLogout = async () => {
    await removeCurrentUserCookie();
    setUser(null);
    window.location.replace(PROJECT_ROUTE.HOME);
  };

  return (
    <header className="bg-white shadow-xl fixed top-0 w-full">
      <nav
        className="mx-auto flex max-w-7xl items-center gap-x-3 gap-y-3 justify-between px-6"
        aria-label="Global"
      >
        <div className="flex lg:flex-1">
          <button
            onClick={() => {
              setLinkClicked(true);
              router.replace(PROJECT_ROUTE.HOME);
            }}
            className="-m-1.5 p-1.5"
          >
            <span className="sr-only">Salt & Pepper</span>
            <img
              className="lg-20 lg:h-28 w-20 lg:w-28"
              src={MainLogo.src}
              alt="Salt & Pepper Logo"
            />
          </button>
        </div>
        <div className="flex lg:hidden order-last">
          <button
            type="button"
            className="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
            onClick={() => setMobileMenuOpen(true)}
          >
            <span className="sr-only">Open main menu</span>
            <Bars3BottomRightIcon className="h-6 w-6" aria-hidden="true" />
          </button>
        </div>
        <div className="hidden sm:flex sm:flex-col sm:w-3/4 lg:w-auto gap-y-3">
          <div className="relative flex">
            <input
              className="w-full py-2 px-4 border border-gray-300 rounded-full shadow-sm focus:outline-none"
              type="search"
              placeholder="Chercher une recette..."
            />
            <button className="absolute inset-y-0 right-0 flex items-center px-4 text-gray-700 bg-sp-primary-400 border hover:bg-sp-primary-300 transition-colors border-gray-300 rounded-r-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <svg
                className="h-5 w-5 text-gray-50"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fillRule="evenodd"
                  clipRule="evenodd"
                  d="M14.795 13.408l5.204 5.204a1 1 0 01-1.414 1.414l-5.204-5.204a7.5 7.5 0 111.414-1.414zM8.5 14A5.5 5.5 0 103 8.5 5.506 5.506 0 008.5 14z"
                />
              </svg>
            </button>
          </div>
          <Popover.Group className="hidden lg:flex lg:gap-x-24">
            <Popover className="relative">
              <Popover.Button className="flex items-center gap-x-1 text-md font-semibold leading-6 text-gray-500 hover:text-sp-primary-400 transition-colors">
                Recettes
                <ChevronDownIcon
                  className="h-5 w-5 flex-none text-grey-900"
                  aria-hidden="true"
                />
              </Popover.Button>

              <Transition
                as={Fragment}
                enter="transition ease-out duration-200"
                enterFrom="opacity-0 translate-y-1"
                enterTo="opacity-100 translate-y-0"
                leave="transition ease-in duration-150"
                leaveFrom="opacity-100 translate-y-0"
                leaveTo="opacity-0 translate-y-1"
              >
                <Popover.Panel className="absolute -left-8 top-full mt-3 w-screen max-w-md overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-gray-900/5">
                  <div className="flex flex-col p-4 gap-y-2">
                    {headerPanelCategories.map((item) => (
                      <div
                        key={item.name}
                        className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm leading-6 bg-gray-50 hover:bg-sp-primary-100 transition-colors"
                      >
                        <div className="flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-sp-primary-100 group-hover:bg-white transition-colors shadow-sm">
                          <item.icon
                            className="h-6 w-6 text-gray-600 group-hover:text-sp-primary-400 transition-colors"
                            aria-hidden="true"
                          />
                        </div>
                        <div className="flex-auto">
                          <a
                            href={item.href}
                            className="block font-semibold text-gray-500"
                          >
                            {item.name}
                            <span className="absolute inset-0" />
                          </a>
                          <p className="mt-1 text-gray-400">
                            {item.description}
                          </p>
                        </div>
                      </div>
                    ))}
                  </div>
                </Popover.Panel>
              </Transition>
            </Popover>
            {headerCategories.map((item) => (
              <a
                key={item.name}
                onClick={() => {
                  setLinkClicked(true);
                  router.replace(item.path);
                }}
                className="text-md font-semibold leading-6 text-gray-500 hover:text-sp-primary-400 transition-colors cursor-pointer"
              >
                {item.name}
              </a>
            ))}
          </Popover.Group>
        </div>
        {userAuthenticated && user ? (
          <Popover.Group className="hidden lg:flex lg:flex-1 lg:justify-end">
            <Popover className="relative">
              <Popover.Button className="flex items-center gap-x-1 text-md font-semibold leading-6 text-gray-500 hover:text-sp-primary-400 transition-colors">
                <img
                  className="inline-block h-12 w-12 rounded-full ring-2 ring-white"
                  src={
                    user.isApiPicture
                      ? process.env.NEXT_PUBLIC_API_USER_PICTURE_URL +
                        PictureService.getPictureUrl(user.pictureName)
                      : process.env.NEXT_PUBLIC_WEB_APP_USER_PICTURE_URL +
                        PictureService.getPictureUrl(user.pictureName)
                  }
                  alt=""
                />
              </Popover.Button>
              <Transition
                as={Fragment}
                enter="transition ease-out duration-200"
                enterFrom="opacity-0 translate-y-1"
                enterTo="opacity-100 translate-y-0"
                leave="transition ease-in duration-150"
                leaveFrom="opacity-100 translate-y-0"
                leaveTo="opacity-0 translate-y-1"
              >
                <Popover.Panel className="absolute -left-64 top-14 mt-3 w-screen max-w-xs overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-gray-900/5 css-popover">
                  <div className="flex flex-col p-4 gap-y-2">
                    <a href="/mon-compte/mon-profil">
                      <div className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm leading-6 bg-gray-50 hover:bg-sp-primary-100 transition-colors">
                        <div className="flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-sp-primary-100 group-hover:bg-white transition-colors shadow-sm">
                          <AccountCircleIcon
                            className="h-6 w-6 text-gray-600 group-hover:text-sp-primary-400 transition-colors"
                            aria-hidden="true"
                          />
                        </div>
                        <div className="flex-auto">
                          <span className="block font-semibold text-gray-500">
                            Mon profil
                          </span>
                        </div>
                      </div>
                    </a>
                    <a href="/mon-compte/mon-profil">
                      <div className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm leading-6 bg-gray-50 hover:bg-sp-primary-100 transition-colors">
                        <div className="flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-sp-primary-100 group-hover:bg-white transition-colors shadow-sm">
                          <DriveFileRenameOutlineIcon
                            className="h-6 w-6 text-gray-600 group-hover:text-sp-primary-400 transition-colors"
                            aria-hidden="true"
                          />
                        </div>
                        <div className="flex-auto">
                          <span className="block font-semibold text-gray-500">
                            Proposer une recette
                          </span>
                        </div>
                      </div>
                    </a>
                    <button
                      onClick={() => handleLogout()}
                      className="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm leading-6 bg-gray-50 hover:bg-sp-primary-100 transition-colors"
                    >
                      <div className="flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-sp-primary-100 group-hover:bg-white transition-colors shadow-sm">
                        <LogoutIcon
                          className="h-6 w-6 text-gray-600 group-hover:text-sp-primary-400 transition-colors"
                          aria-hidden="true"
                        />
                      </div>
                      <p className="block font-semibold text-gray-500">
                        Se déconnecter
                      </p>
                    </button>
                  </div>
                </Popover.Panel>
              </Transition>
            </Popover>
          </Popover.Group>
        ) : (
          <div className="hidden lg:flex lg:flex-1 lg:justify-end">
            <button className="bg-white hover:bg-sp-primary-400 transition-colors text-sp-primary-500 hover:text-gray-50 border-2 border-sp-primary-400 font-bold py-2 px-4 rounded-full inline-flex items-center">
              <a className="flex gap-3" href="/connexion">
                <LockClosedIcon className="h-5 w-5" aria-hidden="true" />
                <span>Connexion</span>
              </a>
            </button>
          </div>
        )}
      </nav>
      <Dialog
        as="div"
        className="lg:hidden"
        open={mobileMenuOpen}
        onClose={setMobileMenuOpen}
      >
        <Dialog.Panel className="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10 css-popover">
          <div className="flex items-center justify-between">
            <div></div>
            <button
              type="button"
              className="-m-2.5 rounded-md p-2.5 text-gray-700"
              onClick={() => setMobileMenuOpen(false)}
            >
              <span className="sr-only">Close menu</span>
              <XMarkIcon className="h-6 w-6" aria-hidden="true" />
            </button>
          </div>
          <div className="mt-6 flow-root">
            <div className="-my-6 divide-y divide-gray-500/10">
              <div className="lg:hidden relative flex my-6">
                <input
                  className="w-full py-2 px-4 border border-gray-300 rounded-full shadow-sm focus:outline-none"
                  type="search"
                  placeholder="Chercher une recette..."
                />
                <button className="absolute inset-y-0 right-0 flex items-center px-4 text-gray-700 bg-sp-primary-400 border hover:bg-sp-primary-300 transition-colors border-gray-300 rounded-r-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <svg
                    className="h-5 w-5 text-gray-50"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      fillRule="evenodd"
                      clipRule="evenodd"
                      d="M14.795 13.408l5.204 5.204a1 1 0 01-1.414 1.414l-5.204-5.204a7.5 7.5 0 111.414-1.414zM8.5 14A5.5 5.5 0 103 8.5 5.506 5.506 0 008.5 14z"
                    />
                  </svg>
                </button>
              </div>
              <div className="space-y-2 py-6">
                <Disclosure as="div" className="-mx-3">
                  {({ open }) => (
                    <>
                      <Disclosure.Button className="flex w-full items-center justify-between rounded-lg py-2 pl-3 pr-3.5 text-base font-semibold leading-7 text-gray-500 hover:bg-sp-primary-100">
                        Recettes
                        <ChevronDownIcon
                          className={classNames(
                            open ? 'rotate-180' : '',
                            'h-5 w-5 flex-none'
                          )}
                          aria-hidden="true"
                        />
                      </Disclosure.Button>
                      <Disclosure.Panel className="mt-2 space-y-2">
                        {[...headerPanelCategories].map((item) => (
                          <Disclosure.Button
                            key={item.name}
                            as="a"
                            href={item.href}
                            className="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-500 hover:bg-sp-primary-100"
                          >
                            {item.name}
                          </Disclosure.Button>
                        ))}
                      </Disclosure.Panel>
                    </>
                  )}
                </Disclosure>
                {headerCategories.map((item) => (
                  <button
                    key={item.name}
                    onClick={() => {
                      setLinkClicked(true);
                      setMobileMenuOpen(false);
                      router.replace(item.path);
                    }}
                    className="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-500 hover:bg-sp-primary-100 cursor-pointer"
                  >
                    {item.name}
                  </button>
                ))}
              </div>
              {userAuthenticated && user ? (
                <div className="space-y-2 py-6">
                  <div className="flex justify-center">
                    <img
                      className="inline-block h-12 w-12 rounded-full ring-2 ring-white"
                      src={
                        user.isApiPicture
                          ? process.env.NEXT_PUBLIC_API_USER_PICTURE_URL +
                            PictureService.getPictureUrl(user.pictureName)
                          : process.env.NEXT_PUBLIC_WEB_APP_USER_PICTURE_URL +
                            PictureService.getPictureUrl(user.pictureName)
                      }
                      alt=""
                    />
                  </div>
                  <a
                    href="/connexion"
                    className="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-500 hover:bg-sp-primary-100"
                  >
                    Mon compte
                  </a>
                  <a
                    href="/connexion"
                    className="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-500 hover:bg-sp-primary-100"
                  >
                    Proposer une recette
                  </a>
                  <button
                    onClick={() => handleLogout()}
                    className="w-full text-left -mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-500 hover:bg-sp-primary-100"
                  >
                    Se déconnecter
                  </button>
                </div>
              ) : (
                <div className="space-y-2 py-6">
                  <a
                    href="/connexion"
                    className="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-500 hover:bg-sp-primary-100"
                  >
                    Connexion
                  </a>
                </div>
              )}
            </div>
          </div>
        </Dialog.Panel>
      </Dialog>
    </header>
  );
}
