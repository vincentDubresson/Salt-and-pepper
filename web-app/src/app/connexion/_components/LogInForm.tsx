'use client';

import { useForm, SubmitHandler } from 'react-hook-form';
import { LoginFormTypes } from '../_lib/FormTypes';
import { useContext } from 'react';
import { AppContext } from '@/app/_lib/_context/AppContext';

export default function LogInForm() {
  const logIn = useContext(AppContext)?.logIn as ({
    // eslint-disable-next-line no-unused-vars
    variables,
  }: {
    variables: { email: string; plainPassword: string };
  }) => void;
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<LoginFormTypes>();

  const onSubmit: SubmitHandler<LoginFormTypes> = async (data) => {
    const email = data.email as string;
    const plainPassword = data.plainPassword as string;
    logIn({
      variables: {
        email: email,
        plainPassword: plainPassword,
      },
    });
  };

  return (
    <>
      <form className="w-full space-y-6" onSubmit={handleSubmit(onSubmit)}>
        <div className="mb-2">
          <label className="block text-sm lg:text-base font-medium leading-6 text-gray-500">
            Adresse e-mail
          </label>
          <input
            className="block w-full border-b-2 px-2.5 py-2.5 bg-sp-primary-50 transition-colors border-b-sp-primary-400 hover:border-b-sp-primary-300 focus:border-b-sp-primary-300 shadow-sm outline-none"
            {...register('email', { required: true })}
          />
          {errors.email && (
            <span className="text-xs lg:text-sm text-red-600">
              L&lsquo;adresse e-mail est obligatoire
            </span>
          )}
        </div>

        <div className="mb-2">
          <div className="flex items-center justify-between">
            <label className="block text-sm lg:text-base font-medium leading-6 text-gray-500">
              Mot de passe
            </label>
            <div className="">
              <a
                href="#"
                className="font-semibold text-sm lg:text-base text-sp-primary-400 hover:text-sp-primary-300 transition-colors "
              >
                Mot de passe oublié ?
              </a>
            </div>
          </div>
          <input
            className="block w-full border-b-2 px-2.5 py-2.5 bg-sp-primary-50 transition-colors border-b-sp-primary-400 hover:border-b-sp-primary-300 focus:border-b-sp-primary-300 shadow-sm outline-none"
            type="password"
            {...register('plainPassword', { required: true })}
          />
          {errors.plainPassword && (
            <span className="text-xs lg:text-sm text-red-600">
              Le mot de passe est obligatoire
            </span>
          )}
        </div>

        <div>
          <button
            className="flex m-auto mt-10 justify-center rounded-full bg-sp-primary-400 px-3.5 py-2 text-sm lg:text-base font-semibold leading-6 text-white shadow-sm transition-colors  hover:bg-sp-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            type="submit"
          >
            Se connecter
          </button>
        </div>
      </form>
    </>
  );
}
